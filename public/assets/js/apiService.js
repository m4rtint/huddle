angular.module( 'apiService', [] )

.factory( 'Register', function ( $resource ) {
    return $resource( '/api/auth/register' );
})

.factory('Activity', function ( $resource ){
    return $resource('/api/activity');
})

.factory( 'Logout', function ( $resource ) {
    return $resource( '/api/auth/logout' );
})

.factory( 'Profile', function ( $resource ) {
    return $resource( '/api/users/:uid/profiles/:pid', { uid: '@uid', pid: '@pid' }, { 'update': { method: 'PUT' } } );
})

.factory( 'Users', function ( $resource ) {
    return $resource( '/api/users/:id', { id: '@id' }, { 'update': { method: 'PUT' } } );
})

.factory( 'UsersRoles', function ( $resource ) {
    return $resource( '/api/users-roles/' );
})

.factory( 'Managers', function ( $resource ) {
    return {

        conferences: function () {
            return $resource( '/api/conferences/:cid/managers/:uid', { uid: '@uid', cid: '@cid'} );
        },

        events: function () {
            return $resource( '/api/events/:eid/managers/:uid', {uid: '@uid', eid: '@eid'} );
        }

    }
})

.factory( 'Roles', function ( $resource ) {
    return $resource( '/api/roles/:id', { id: '@id' }, { 'update': { method: 'PUT' } } );
})

.factory( 'Conferences', function( $resource ) {
    return {

        fetch: function () {
            return $resource( '/api/conferences/:cid', {cid: '@cid'}, {'update': { method: 'PUT' }} );
        },

        status: function () {
            return $resource( '/api/conferences-status/:cid', {cid: '@cid'}, {'update': { method: 'PUT' }} );
        },

        attendees: function () {
            return $resource( '/api/conferences/:cid/profiles/:pid', {cid: '@cid', pid: '@pid'}, {'update': { method: 'PUT' }} );
        },

        vehicles: function () {
            return $resource( '/api/conferences/:cid/vehicles/:type/:vid', {cid: '@cid', type: '@type', vid: '@vid'}, {'update': { method: 'PUT' }} );
        },

        inventory: function () {
            return $resource( '/api/conferences/:cid/inventory/:id', {cid: '@cid', iid: '@id'}, {'update': { method: 'PUT' }} );
        },

        accommodations: function () {
            return $resource( '/api/conferences/:cid/accommodations/:aid', {cid: '@cid', aid: '@aid'}, {'update': { method: 'PUT' }} );
        },

        rooms: function () {
            return $resource( '/api/conferences/:cid/accommodations/:aid/:rid', {cid: '@cid', accId: '@aid', rid: '@rid'}, {'update': { method: 'PUT' }} );
        }
    }
})

.factory( 'Events', function ( $resource ) {
    return {

        status: function () {
            return $resource( '/api/events-status/:eid', {eid: '@eid'}, {'update': { method: 'PUT' }} );
        },

        fetch: function () {
            return $resource( '/api/conferences/:cid/events/:eid', { cid: '@cid', eid: '@eid' }, { 'update': { method: 'PUT' } } );
        },

        attendees: function () {
            return $resource( '/api/events/:eid/attendees/:aid', { eid: '@eid', attId: '@aid' }, { 'update': { method: 'PUT' } } );
        },

        vehicles: function () {
            return $resource( '/api/events/:eid/vehicles/:type/:vid', { eid: '@eid', type: '@type', vid: '@vid' }, { 'update': { method: 'PUT' } } );
        }

    }
})
