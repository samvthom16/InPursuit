var memberSingle = require( '../pages/member-single.js' );

//console.log( memberSingle.options.template );

module.exports = [
	{
		path			: '/',
		component	: require( '../pages/home.js' )
	},

	{
		path			: '/dashboard',
		component	: require( '../pages/dashboard.js' )
	},
	{
		path			: '/members',
		component	: require( '../pages/members.js' )
	},
	{
		path			: '/members/new',
		component	: require( '../pages/member-new.js' )
	},
	{
		name			: 'SingleMember',
		path			: '/members/:id',
		component	: require( '../pages/member-single.js' )
	},
	{
		name			: 'SingleMemberEdit',
		path			: '/members/:id/edit',
		component	: require( '../pages/member-single-edit.js' )
	},
	{
		path			: '/events',
		component	: require( '../pages/events.js' )
	},
	{
		path			: '/events/new',
		component	: require( '../pages/event-new.js' )
	},
	{
		name			: 'SingleEvent',
		path			: '/events/:id',
		component	: require( '../pages/event-single.js' )
	},
	{
		name			: 'SingleEventEdit',
		path			: '/events/:id/edit',
		component	: require( '../pages/event-single-edit.js' )
	},
	{
		path			: '/managers',
		component	: require( '../pages/managers.js' )
	},
	{
		path			: '/managers/new',
		component	: require( '../pages/manager-new.js' )
	},
	{
		name			: 'SingleManager',
		path			: '/managers/:id',
		component	: require( '../pages/manager-single.js' )
	},
	{
		name			: 'SingleManagerEdit',
		path			: '/managers/:id/edit',
		component	: require( '../pages/manager-single-edit.js' )
	}
];
