'use strict';

var dust = require('dustjs-linkedin');
var mongoose = require('mongoose');
var passport = require('passport');
var LocalStrategy = require('passport-local');

var User = require('../models/user');

module.exports = function(config) {

  mongoose.connect(config.get('mongodb:uri', 'mongodb://localhost/my_database'));

  require('mongoose').Promise = require('bluebird');

  passport.use(new LocalStrategy({
      usernameField: '_username',
      passwordField: '_password',
      passReqToCallback: true,
    },
    function(req, username, password, done) {
      User.authenticate(username, password, function (err, user) {
        if (err) {
          return done(err);
        } else if (!user) {
          req.flash('warn', 'user.errors.password');
          return done(false);
        }
        return done(null, user);
      });
    }
  ));

  passport.serializeUser(function(user, done) {
    done(null, user.id);
  });

  passport.deserializeUser(function(id, done) {
    User.findById(id, function (err, user) {
      done(err, user);
    });
  });

};
