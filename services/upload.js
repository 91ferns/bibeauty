'use strict';

var AWS = require('aws-sdk');
var s3Stream = require('s3-upload-stream')(new AWS.S3());

module.exports = s3Stream;
