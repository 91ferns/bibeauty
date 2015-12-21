'use strict';

var auth = require('../../services/auth');

module.exports = function (router) {

  router.get('/businesses/new', auth.user.businesses(), function(req, res) {
    res.render('admin/businesses/new', {});
  });

};
