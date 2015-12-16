'use strict';

var util = require('util');

var Bluebird = require('bluebird');
var _ = require('lodash');

var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var emailValidator = require('email-validator');
var geocoder = require('geocoder');
var phone = require('phone');
var slug = require('slug');

function generateRand() {
  return Math.round(Math.rand() * 10000 );
}

function getProperTimes() {

}

var ReviewSchema = new Schema({
  rating: { type: Number, required: true, default: 1 },
  visitedDate: { type: Date, default: Date.now, required: true },
  content: { type: String, required: true },
  reports: { type: Number, default: 0 },
  verified: { type: Boolean, default: false },
  _poster: { type: Schema.ObjectId, ref: 'User', default: null },
});

var OfferSchema = new Schema({
  availabilitySet: {
    startDate: { type: Date, required: true },
    daysOfTheWeek: [{ type: String, enum: 'Sunday Monday Tuesday Wednesday Thursday Friday Saturday'.split(' ')}],
    times: [{ type: String, enum: getProperTimes() }],
    recurrenceType: { type: String, enum: 'never weekly monthly daily'.split(' ') },
  },
  processed: { type: Boolean, default: false },
  createdAt: { type: Date, default: Date.now },
  currentPrice: { type: Number, required: '{PATH} is required for offers' }, // Need to validate it to be lower than its parent treatment
  isEnabled: { type: Boolean, default: true }
});

OfferSchema
  .path('currentPrice')
  .validate(function(v) {
    var originalPrice = this.parent().originalPrice;
    return v < originalPrice;
  }, 'Must be cheaper than the original price');

var TreatmentSchema = new Schema({
  duration: { type: Number, max: 520, min: 15, required: '{VALUE} is not a valid duration (in minutes)' },
  name: { type: String, required: true },
  description: { type: String, required: '{PATH} is required for a treatment' },
  originalPrice: { type: Number, required: '{PATH} is required for treatments' },
  _therapist: { type: Schema.ObjectId, default: null },
  _category: { type: Schema.ObjectId, ref: 'TreatmentCategory' },
  offers: [OfferSchema]
});

TreatmentSchema.virtual('therapist')
  .get(function() {
    if (!this._therapist) {
      return 'None';
    }

    var therapists = this.parent().therapists;
    return _.findWhere(therapists, { _id: this._therapist });
  });

TreatmentSchema.virtual('durationReadable')
  .get(function() {
    var duration = this.duration;

    var minutes = duration % 60;
    var hours = Math.floor(duration / 60);

    var string = '';

    if (hours > 0) {
      string += util.format('%d hr%s ', hours, hours === 1 ? '' : 's');
    }

    string += util.format('%d min%s', minutes, minutes === 1 ? '' : 's');

    return string;

  });

TreatmentSchema.virtual('label')
  .get(function() {
    if (!this._category) {
      return;
    }
    return this._category.label;
  });

TreatmentSchema.methods = {
  getCheapestOffer: function() {

  },
  getCheapestDiscountPrice: function() {

  },
  getCheapestDiscountPercentage: function() {

  }
};

var TherapistSchema = new Schema({
  name: { type: String, required: true },
  title: { type: String },
  phone: { type: String },
  email: { type: String },
  active: { type: Boolean, default: true }
});

var Business;

var BusinessSchema = new Schema({
  name: { type: String, required: 'Please enter your {PATH}', trim: true },
  rand: { type: Number, default: generateRand },
  slug: { type: String, required: 'Slug is required', trim: true, lowercase: true },
  websiteUrl: { type: String },
  email: { type: String, validate: {
    validator: emailValidator.validate,
    message: '{VALUE} is not a valid email address'
  }, required: true, trim: true },
  active: { type: Boolean, default: false },
  paymentsAccepted: {
    cash: { type: Boolean, default: true },
    credit: { type: Boolean, default: false }
  },
  address: {
    street: { type: String },
    line2: { type: String, default: '' },
    city: { type: String, default: 'Los Angeles' },
    state: { type: String, maxlength: 2, default: 'CA' },
    zip: { type: String, validate: {
      message: '{VALUE} is an invalid zip code'
    }, required: true },
    country: { type: String, default: 'US' },
    coordinates: {
      latitude: { type: Number, default: null },
      longitude: { type: Number, default: null }
    }
  },
  phones: {
    landline: { type: String },
    mobile: { type: String },
  },
  reviews: [ReviewSchema],
  yelpLink: { type: String, required: 'Please enter your business\'s {PATH}', trim: true, lowercase: true },
  description: { type: String, required: 'Please enter your business\'s {PATH}', trim: true },
  _owner: { type: Schema.ObjectId, ref: 'User', required: true },

  attachments: {
    header: { type: Schema.ObjectId, ref: 'Attachment' },
    logo: { type: Schema.ObjectId, ref: 'Attachment' }
  },

  treatments: [TreatmentSchema],
  therapists: [TherapistSchema],

  // Timestamps
  updatedAt: { type: Date, default: Date.now },
  createdAt: { type: Date, default: Date.now }

});

BusinessSchema.virtual('reviews.average')
  .get(function() {
    var total = 0;
    var totalNum = 0;

    _.forEach(this.reviews, function(review) {
      total += review.rating;
      totalNum++;
    });

    return (total / totalNum).toFixed(1);

  });

BusinessSchema.virtual('landline')
  .get(function() {
    return this.phones.landline;
  })
  .set(function(v) {
    var formattedNumber = phone(v);
    this.phones.landline = formattedNumber[0];
  });

  BusinessSchema.virtual('mobile')
    .get(function() {
      return this.phones.mobile;
    })
    .set(function(v) {
      var formattedNumber = phone(v);
      this.phones.mobile = formattedNumber[0];
    });

BusinessSchema.virtual('address.full')
  .get(function() {
    var string = this.address.street;
    string += ',' + this.address.city;
    string += ',' + this.address.state;
    string += ',' + this.address.country;

    return string;
  });

BusinessSchema.virtual('googleMaps.url')
  .get(function() {
    return 'http://maps.google.com/?q=' + this.address.full;
  });

BusinessSchema.pre('save', function(next) {
  this.updatedAt = Date.now;
  next();
});

BusinessSchema.pre('validate', function(next) {
  this.slug = slug(this.name);
  next();
});

BusinessSchema.methods = {
  geocodeZip: function(cb) {
    geocoder.geocode(this.address.full, function ( err, data ) {
      // do something with data
      if (err) {
        return cb(err);
      }

      var returnedLocation = data.results.geometry.location || false;
      if (!returnedLocation) {
        return cb(new Error('Could not find longitude and latitude'));
      }

      this.address.coordinates.latitude = returnedLocation.lat;
      this.address.coordinates.longitude = returnedLocation.lng;

      cb(null, this.address.coordinates);

    });
  }
};

BusinessSchema.statics = {
  getStates: function() {
    return {
      'AL': 'Alabama',
      'AK': 'Alaska',
      'AS': 'American Samoa',
      'AZ': 'Arizona',
      'AR': 'Arkansas',
      'CA': 'California',
      'CO': 'Colorado',
      'CT': 'Connecticut',
      'DE': 'Delaware',
      'DC': 'District Of Columbia',
      'FM': 'Federated States Of Micronesia',
      'FL': 'Florida',
      'GA': 'Georgia',
      'GU': 'Guam',
      'HI': 'Hawaii',
      'ID': 'Idaho',
      'IL': 'Illinois',
      'IN': 'Indiana',
      'IA': 'Iowa',
      'KS': 'Kansas',
      'KY': 'Kentucky',
      'LA': 'Louisiana',
      'ME': 'Maine',
      'MH': 'Marshall Islands',
      'MD': 'Maryland',
      'MA': 'Massachusetts',
      'MI': 'Michigan',
      'MN': 'Minnesota',
      'MS': 'Mississippi',
      'MO': 'Missouri',
      'MT': 'Montana',
      'NE': 'Nebraska',
      'NV': 'Nevada',
      'NH': 'New Hampshire',
      'NJ': 'New Jersey',
      'NM': 'New Mexico',
      'NY': 'New York',
      'NC': 'North Carolina',
      'ND': 'North Dakota',
      'MP': 'Northern Mariana Islands',
      'OH': 'Ohio',
      'OK': 'Oklahoma',
      'OR': 'Oregon',
      'PW': 'Palau',
      'PA': 'Pennsylvania',
      'PR': 'Puerto Rico',
      'RI': 'Rhode Island',
      'SC': 'South Carolina',
      'SD': 'South Dakota',
      'TN': 'Tennessee',
      'TX': 'Texas',
      'UT': 'Utah',
      'VT': 'Vermont',
      'VI': 'Virgin Islands',
      'VA': 'Virginia',
      'WA': 'Washington',
      'WV': 'West Virginia',
      'WI': 'Wisconsin',
      'WY': 'Wyoming'
    };
  }
};

BusinessSchema.index({ slug: 'text', email: 'text' });

module.exports = Business = mongoose.model('Business', BusinessSchema);
