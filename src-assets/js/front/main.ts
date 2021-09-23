import axios from 'axios';
const qs = require('qs');

(window as any).pixCopyText = function ( value: string, id: string ) 
{
	if ( navigator.clipboard )
	{
		navigator
			.clipboard
			.writeText(value)
			.then(() => { (window as any).pixCopied(id); })
			.catch(() => { (window as any).pixCopyFallback(value, id); });
	}
	else
	{ (window as any).pixCopyFallback(value, id); }
};

(window as any).pixCopyFallback = function ( value: string, id: string )
{
	const el = document.createElement('textarea');
	
	el.value = value;
	el.setAttribute('readonly', '');
	el.style.position = 'absolute';
	el.style.left = '-9999px';
	document.body.appendChild(el);
	
	el.select();  
	el.setSelectionRange(0, 99999);

	const copy = document.execCommand('copy');
	document.body.removeChild(el);

	if ( copy )
	{ (window as any).pixCopied(id); }

	if ( window.getSelection() !== null && window.getSelection() !== undefined )
	{ (window.getSelection() as any).removeAllRanges(); }
};

(window as any).pixCopied = function (id: string) {
	const el: any = document.getElementById(id);
	const cn = el.innerHTML;
	el.innerHTML = 'Copiado';

	setTimeout(() => { el.innerHTML = cn; }, 1500);
};

export interface IPglyPixWebhookOptions {
	tries: number,
	each: number,
	txid: string,
	action: string,
	url: string,
	redirect_to: string,
	x_security: string,
	debug: boolean
}

export interface IPglyPixWebhook
{
	options: IPglyPixWebhookOptions;
	_init(): void;
	_axios(): Promise<boolean>;
}

function PglyPixWebhook ( this: IPglyPixWebhook, options: IPglyPixWebhookOptions )
{
	options.action = options.action || 'pgly_wc_piggly_pix_webhook';

	if ( !options.url )
	{ throw new Error('`url` is required to update Pix.'); }

	if ( !options.txid )
	{ throw new Error('`txid` is required to update Pix.'); }

	if ( !options.action )
	{ throw new Error('`action` is required to update Pix.'); }

	if ( !options.redirect_to )
	{ throw new Error('`redirect_to` is required to update Pix.'); }

	if ( !options.x_security )
	{ throw new Error('`x_security` is required to update Pix.'); }

	options.debug = options.debug || false;
	options.tries = options.tries || 60;
	options.each = options.each || 2000;

	if ( options.debug )
	{ console.log('Options', options); }
	
	this.options = options;
	this._init();
}

PglyPixWebhook.prototype._init = async function (this: IPglyPixWebhook) 
{
	for ( let i = 0; i < this.options.tries; i++ )
	{ await this._axios(); }
};

PglyPixWebhook.prototype._axios = function (this: IPglyPixWebhook) 
{
	return new Promise((resolve, reject) => {
		const data = {
			txid: this.options.txid,
			action: this.options.action,
			xSecurity: this.options.x_security
		}

		if ( this.options.debug )
		{ console.log('Data', data); }

		axios
			.post(
				this.options.url,
				qs.stringify(data)
			)
			.then( res => {
				const { data } = res;
				console.log(data.data.message ?? 'Resposta inválida');

				if ( data.success )
				{ 
					window.location.href = this.options.redirect_to;
					return setTimeout(() => resolve(true), this.options.each);
				}

				return setTimeout(() => resolve(false), this.options.each);
			})
			.catch(err => setTimeout(() => reject(err), this.options.each));
	});
};


(window as any).PglyPixWebhook = PglyPixWebhook;