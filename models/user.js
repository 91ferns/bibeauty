'use strict';

var Bluebird = require('bluebird');
var _ = require('lodash');

var mongoose = require('mongoose');
var Schema = mongoose.Schema;
var crypto = require('crypto');
var authTypes = ['facebook'];

var User;

var UserSchema = new Schema({
  name: { type: String, required: 'Please enter your {PATH}', trim: true },
  username: { type: String, required: 'A unique {PATH} is required', validate: /^\w{3,15}$/, trim: true, lowercase: true, index: true  },
  email: { type: String, lowercase: true, trim: true, index: true },
  role: {
    type: String,
    default: 'user',
  },
  hashedPassword: String,
  provider: String,
  salt: String,
  google: {}, //thumbnail: { type: String, default: '/assets/images/placeholder.png' },
});

UserSchema.index({ username: 'text', email: 'text' });

module.exports = User = mongoose.model('User', UserSchema);
