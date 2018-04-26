import { extend } from 'flarum/extend';
import app from 'flarum/app';
import PermissionGrid from 'flarum/components/PermissionGrid';

app.initializers.add('flagrow-guardian', () => {

  extend(PermissionGrid.prototype, 'startItems', items => {
    items.add('actWithoutFootprint', {
      icon: 'fa fa-shield-alt',
      label: app.translator.trans('flagrow-guardian.admin.permissions.act_without_footprint'),
      permission: 'withoutFootprint',
      allowGuest: true
    }, 5);
  });
});
