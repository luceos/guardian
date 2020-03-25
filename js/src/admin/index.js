import {extend} from 'flarum/extend';
import app from 'flarum/app';
import PermissionGrid from 'flarum/components/PermissionGrid';

app.initializers.add('fof-guardian', () => {
    extend(PermissionGrid.prototype, 'startItems', items => {
        items.add('actWithoutFootprint', {
            icon: 'fas fa-shield-alt',
            label: app.translator.trans('flagrow-guardian.admin.permissions.act_without_footprint'),
            permission: 'actWithoutFootprint',
            allowGuest: true
        }, 5);
        items.add('actWithoutFlooding', {
            icon: 'fas fa-shipping-fast',
            label: app.translator.trans('flagrow-guardian.admin.permissions.act_without_floodgate'),
            permission: 'actWithoutFlooding',
            allowGuest: true
        }, 5);
    });
});
