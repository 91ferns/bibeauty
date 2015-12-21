'use strict';

var auth = require('../../services/auth');

module.exports = function (router) {

  router.post('/', auth.user.isAuthenticated(), function(req, res) {
    res.json('hai');
  });

  router.get('/new', auth.user.businesses(), function(req, res) {
    res.render('admin/businesses/new', {});
  });

};
