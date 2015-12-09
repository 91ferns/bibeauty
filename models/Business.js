'use strict';

var Bluebird = require('bluebird');
var _ = require('lodash');

var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var Business;

var BusinessSchema = new Schema({
  name: { type: String, required: 'Please enter your {PATH}', trim: true },
  rand: { type: Number, default: }
});

UserSchema.index({ name: 'text', email: 'text' });

module.exports = User = mongoose.model('User', UserSchema);
