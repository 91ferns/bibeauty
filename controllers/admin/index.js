'use strict';

var auth = require('../../services/auth');

module.exports = function (router) {


  router.post('/businesses', auth.user.isAuthenticated(), function(req, res) {
    res.json('hai');
  });

  router.get('/businesses/new', auth.user.businesses(), function(req, res) {
    res.render('admin/businesses/new', {});
  });

  router.get('/', auth.user.businesses(), function (req, res) {
    res.render('admin/index', {});
  });

};
