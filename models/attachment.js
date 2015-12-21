'use strict';

var Bluebird = require('bluebird');
var _ = require('lodash');

var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var s3Stream = require('../services/upload');

var AttachmentSchema = new Schema({
  key: { type: String, required: true, unique: true },
  mime: { type: String },
  filename: { type: String },
  size: { type: Number, default: 0 },
  processed: { type: Boolean, default: false },
});

AttachmentSchema.methods = {
  createS3WriteStream: function() {
    var writeStream = s3Stream.upload({
      Bucket: 'bucket-name',
      Key: this.key,
      ACL: 'public-read',
      ContentType: this.mime || 'binary/octet-stream',
    });

    return writeStream;
  }
};

AttachmentSchema.statics = {
  createFromRawFileData: function(file, filename, encoding, mimetype) {
    return this.create({
      filename: filename,
      mime: mimetype,
      key: filename,
      size: 0
    });
  }
};
