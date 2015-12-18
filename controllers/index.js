'use strict';

var passport = require('passport');
var User = require('../models/user');

module.exports = function (router) {

  router.get('/', function (req, res) {
    res.render('index', {});
  });

  router.get('/login', function(req, res) {
    // res.locals.locale = 'it-IT'; // Locale
    res.render('login', {});
  });

  router.post('/login', passport.authenticate('local', { failureRedirect: '/login' }), function(req, res) {
    res.redirect('/');
  });

  router.get('/signup', function(req, res) {
    res.locals.locale = 'it-IT'; // Locale
    res.render('signup', {});
  });

  router.post('/signup', function(req, res) {

    User.signup(req.body, function(err, user) {
      if (err) {
        req.flash('info', err.toString());
        return res.redirect('/signup');
      }

      if (!user) {
        req.flash('info', 'Something is wrong with your entry');
        return res.redirect('/signup');
      }

      req.flash('success', 'Thanks for signing up');
      return res.redirect('/login');



    });

  });

  router.get('/search', function(req, res) {
    res.render('search', {});
  });

};
