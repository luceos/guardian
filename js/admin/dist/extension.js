'use strict';

System.register('flagrow/guardian/main', ['flarum/extend', 'flarum/app', 'flarum/components/PermissionGrid'], function (_export, _context) {
  "use strict";

  var extend, app, PermissionGrid;
  return {
    setters: [function (_flarumExtend) {
      extend = _flarumExtend.extend;
    }, function (_flarumApp) {
      app = _flarumApp.default;
    }, function (_flarumComponentsPermissionGrid) {
      PermissionGrid = _flarumComponentsPermissionGrid.default;
    }],
    execute: function () {

      app.initializers.add('flagrow-guardian', function () {

        extend(PermissionGrid.prototype, 'startItems', function (items) {
          items.add('actWithoutFootprint', {
            icon: 'fa fa-shield-alt',
            label: app.translator.trans('flagrow-guardian.admin.permissions.act_without_footprint'),
            permission: 'withoutFootprint',
            allowGuest: true
          }, 5);
        });
      });
    }
  };
});