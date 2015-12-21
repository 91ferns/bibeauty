'use strict';

var _ = require('lodash');

var session = require('express-session');
var RedisStore = require('connect-redis')(session);

// var dust = require('dustjs-linkedin');
var flash = require('flash');
var mongoose = require('mongoose');
var passport = require('passport');
var LocalStrategy = require('passport-local');

var routes = require('./routes.json').routes;

var User = require('../models/user');

function matchRoute(url, route) {
  // First replace the :params in the route and turn them into regex
  var regexRoute = route.replace(/([:][^\/]+)(\/?)/, '([:][^\/]+)');
  var matches = url.match(new RegExp('^' + regexRoute + '$')) || false;
  return matches;
}

module.exports = function(config, app) {


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

  app.use(session({
    store: new RedisStore(config.get('redis')),
    secret: config.get('session:secret')
  }));
  app.use(flash());
  app.use(passport.initialize());
  app.use(passport.session());
  app.use(function(req, res, next) {
    res.locals.user = req.user || false; //console.log(req.user);
    next();
  });

  app.use(function(req, res, next) {
    // We need to match the route
    var route = _.findKey(routes, function(v) {
      return matchRoute(req.url, v);
    });

    if (route) {
      res.locals.route = route;
      req.route = route;
    }

    next();

  });



};
