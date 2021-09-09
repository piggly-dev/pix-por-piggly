export default class Payload {
	protected data: { [key: string]: any };

	constructor ()
	{ this.data = {}; }

	set ( name: string, value: any ) : Payload
	{
		if ( typeof value === 'object' && value !== null && !Array.isArray(value) )
		{ this.data[name] = new Payload().import(value); }
		else
		{ this.data[name] = value; }

		return this;
	}

	get ( name: string, _default: any = undefined ) : any
	{ return this.data[name] ?? _default; }

	import ( value: { [key: string]: any } ) : Payload
	{
		Object.keys(value).forEach(key => {
			this.set(key, value[key]);
		});

		return this;
	}

	export () : object
	{
		const data: { [key: string]: any } = {};

		Object.keys(this.data).forEach(k => {
			if ( this.data[k] instanceof Payload )
			{ data[k] = this.data[k].export(); }
			else
			{ data[k] = this.data[k]; }
		});

		return data;
	}
}