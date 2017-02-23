/**
 * Retrieves active drupal.org issues for all D8 projects in composer.json
 *
 * Installation: run 'npm install'
 *
 * Usage: run 'node listIssues.js critical' or 'node listIssues.js major',
 * ensuring that composer.json is in the same directory.
 */

const fs = require('fs');
const request = require('request');

if (!fs.existsSync('composer.json')) {
  console.log('composer.json does not exist!');
  process.exit();
}

const priority = process.argv[2];

if (priority !== 'major' && priority !== 'critical') {
  console.log(`Usage: 'node listIssues.js critical' or 'node issues.js major'`);
  process.exit();
}

const data = JSON.parse(fs.readFileSync('composer.json', 'utf8'));
const projects = Object.keys(data.require)
  .filter(item => /^drupal\//.test(item))
  .map(item => item.replace(/^drupal\//, ''));

const getIssues = function (url, projectTitle) {
  request(url, (err, res, body) => {
    const issuesJson = JSON.parse(body);
    const validStatuses = [
      '1', // Active
      '4', // PP
      '8', // NR
      '13', // NW
      '14' // RTBC
    ];
    // Get D8 issues with a valid status.
    const issuesList = issuesJson.list
      .filter(item => item.field_issue_version.indexOf('8.x') === 0)
      .filter(item => validStatuses.indexOf(item.field_issue_status) > -1);
    if (!issuesList.length) {
      // No issues :)
      return;
    }
    let title;
    if (priority === 'critical') {
      title = `Critical issues for ${projectTitle}`;
    } else if (priority === 'major') {
      title = `Major issues for ${projectTitle}`;
    }
    console.log(title);
    console.log('='.repeat(title.length));
    console.log('');
    issuesList.forEach(item => {
      console.log(item.title);
      console.log(`http://www.drupal.org/node/${item.nid}`);
      console.log(`Created: ${new Date(+item.created * 1000).toLocaleString()}`);
      //console.log(`Updated: ${new Date(+item.changed * 1000).toLocaleString()}`);
      console.log('');
    });
    console.log('');
    if (issuesJson.next) {
      // add .json
      const nextUrl = issuesJson.next.replace(/api-d7\/node\?type/, 'api-d7/node.json?type');
      getIssues(nextUrl, projectTitle);
    }
 });
};

// Get issue number for every project.
projects.forEach(project => {
  request(`https://www.drupal.org/api-d7/node.json?field_project_machine_name=${project}`, (err, res, body) => {
    const json = JSON.parse(body);
    if (!json.list[0]) {
      // No such project (i.e. drupal core)
    } else {
      const nid = json.list[0].nid;
      let issuePriority;
      if (priority === 'critical') {
        issuePriority = '400';
      }
      else if (priority === 'major') {
        issuePriority = '300';
      }
      getIssues(`https://www.drupal.org/api-d7/node.json?type=project_issue&field_project=${nid}&field_issue_priority=${issuePriority}&sort=field_issue_version&direction=DESC`, json.list[0].title);
    }
  });
});
