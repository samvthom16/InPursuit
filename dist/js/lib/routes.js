var routes = [
	{
		path			: '/',
		component	: require( './pages/home.js' )
	},

	{
		path			: '/dashboard',
		component	: require( './pages/dashboard.js' )
	},
	{
		path			: '/members',
		component	: require( './pages/members.js' )
	},
	{
		path			: '/events',
		component	: require( './pages/events.js' )
	},
	/*
	{
		path			: '/members/:id',
		component	: memberLayout
	},
	{
		path			: '/members-:id/edit',
		component	: memberEditLayout
	},
	*/
];

module.exports = routes;
