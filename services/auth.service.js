'use strict';

var mongoose = require('mongoose');
var passport = require('passport');
var jwt = require('jsonwebtoken');
var expressJwt = require('express-jwt');
var compose = require('composable-middleware');
var config = require('confit');

var validateJwt = expressJwt({ secret: config.get('secrets:session') });

var _ = require('lodash');

var User = require('../models/user');

/**
 * Attaches the user object to the request if authenticated
 * Otherwise returns 403
 */
function isAuthenticated() {
  return compose()

    // Validate jwt
    .use(function(req, res, next) {
      // allow access_token to be passed through query parameter as well
      if (req.query && req.query.hasOwnProperty('access_token')) {
        // jscs:disable
        req.headers.authorization = 'Bearer ' + req.query.access_token;
        // jscs:enable
      }

      validateJwt(req, res, next);
    })

    // Attach user to request
    .use(function(req, res, next) {

      if (req.user && req.user.hasOwnProperty('username')) {
        return next();
      }

      User.findById(req.user._id, function(err, user) {
        if (err) {
          return next(err);
        }
        if (!user) {
          return res.send(401);
        }

        req.user = user;
        next();
      });
    });
}

/**
 * Checks if the user role meets the minimum requirements of the route
 */
function hasRole(roleRequired) {
  if (!roleRequired) {
    throw new Error('Required role needs to be set');
  }

  return compose()
    .use(isAuthenticated())
    .use(function meetsRequirements(req, res, next) {
      if (req.user.hasRole(roleRequired)) {
        next();
      } else {
        res.send(403);
      }
    });
}

/**
 * Returns a jwt token signed by the app secret
 */
function signToken(id) {
  return jwt.sign({ _id: id }, config.secrets.session, { expiresIn: 60 * 60 * 5 });
}

/**
 * Set token cookie directly for oAuth strategies
 */
function setTokenCookie(req, res) {
  if (!req.user) {
    return res.json(404, { message: 'Something went wrong, please try again.'});
  }
  var token = signToken(req.user._id, req.user.role);
  res.cookie('token', JSON.stringify(token));
  res.redirect('/');
}

/**
 * Parse through params
 */
function parseDynamicParams(dynamicParams, req) {
  var params = {};

  _.forIn(dynamicParams, function(value, key) {
    params[key] = req.params[value] || '';
  });

  return params;
}

// User related authentication
exports.user = {
  isAuthenticated: isAuthenticated,
  hasRole: hasRole,
};

// Other
exports.signToken = signToken;
exports.setTokenCookie = setTokenCookie;
