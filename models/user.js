'use strict';

var Bluebird = require('bluebird');
var _ = require('lodash');

var mongoose = require('mongoose');
var Schema = mongoose.Schema;
var crypto = require('crypto');
var emailValidator = require('email-validator');

var User;

var UserSchema = new Schema({
  firstName: { type: String, required: 'Please enter your {PATH}', trim: true },
  lastName: { type: String, required: 'Please enter your {PATH}', trim: true },
  email: { type: String, required: 'A unique {PATH} is required', trim: true, lowercase: true, index: true  },
  role: {
    type: String,
    default: 'user',
  },
  hashedPassword: String,
  salt: String,
});

UserSchema
  .path('hashedPassword')
  .validate(function(v) {
    if (this._password || this._passwordConfirm) {
      if (this._password !== this._passwordConfirm) {
        return false;
      }
    }
    return true;

  }, 'Password must match confirmation');

UserSchema
  .path('email')
  .validate(function(v) {
    return emailValidator.validate(v);
  }, '{VALUE} is not a valid email')
  .validate(function(value, respond) {
    var _this = this;
    this.constructor.findOne({email: value}, function(err, user) {
      if (err) {
        throw err;
      }
      if (user) {
        if (_this.id === user.id) {
          return respond(true);
        }
        return respond(false);
      }

      respond(true);
    });
  }, 'The specified email address is already in use.');

/**
 * Virtuals
 */
UserSchema
  .virtual('password')
  .set(function(password) {
    this._password = password;
    this.salt = this.makeSalt();
    this.hashedPassword = this.encryptPassword(password);
  })
  .get(function() {
    return this._password;
  });

UserSchema
  .virtual('passwordConfirm')
  .get(function() {
    return this._passwordConfirm;
  })
  .set(function(value) {
    this._passwordConfirm = value;
  });

UserSchema.methods = {
  /**
    * Function checks if the user role is equal to or greater than what is required
    *
    * Function uses configuration file in the environments folder to determine
    * which role is greater than the other
    */

  hasRole: function(role) {
    return false;
  },

  /**
   * Authenticate - check if the passwords are the same
   *
   * @param {String} plainText
   * @return {Boolean}
   * @api public
   */
  authenticate: function(plainText) {
    return this.encryptPassword(plainText) === this.hashedPassword;
  },

  /**
   * Make salt
   *
   * @return {String}
   * @api public
   */
  makeSalt: function() {
    return crypto.randomBytes(16).toString('base64');
  },

  /**
   * Encrypt password
   *
   * @param {String} password
   * @return {String}
   * @api public
   */
  encryptPassword: function(password) {
    if (!password || !this.salt) {
      return '';
    }
    var salt = new Buffer(this.salt, 'base64');
    return crypto.pbkdf2Sync(password, salt, 10000, 64).toString('base64');
  },
};

UserSchema.statics = {
  authenticate: function(username, password, cb) {
    User.findOne({ email: username })
      .then(function(user) {
        // We have the user.
        if (!user || !user.authenticate(password)) {
          return cb(null, false);
        }

        cb(null, user);

      })
      .catch(cb);
  },

  signup: function(params, cb) {
    // Helper function because we don't just want to pass the whole thing in create.
    // We also would rather deal with param security here

    // We do not need to do much validation here. The model will do it
    var user = new this();
    user.email = params.email;
    user.firstName = params.firstName;
    user.lastName = params.lastName;
    user.password = params.password;
    user.passwordConfirm = params.passwordConfirm;

    return user.save(cb);

  }
};

UserSchema.index({ email: 'text' });

module.exports = User = mongoose.model('User', UserSchema);
