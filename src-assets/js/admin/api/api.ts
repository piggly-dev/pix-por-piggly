import axios from "./index";
import qs from 'qs';
import { Settings } from "@/store";

export default {
	getSettings: async () : Promise<Settings> => {
		console.log(`POST pgly_wc_piggly_pix_get_plugin_settings ${window.wcPigglyPix.ajax_url}`);

		try
		{
			const { data } = await axios.post(window.wcPigglyPix.ajax_url, qs.stringify({
				action: 'pgly_wc_piggly_pix_get_plugin_settings',
				xSecurity: window.wcPigglyPix.x_security
			}));

			if ( !data.success )
			{ throw new Error(data.data.message); }

			console.log(data.data);
			return data.data as Settings;
		}
		catch ( err )
		{ 
			console.error(err);
			throw err; 
		}
	},

	saveSettings: async ( section: string, postData: object ) : Promise<boolean> => {
		console.log(`POST pgly_wc_piggly_pix_set_plugin_settings ${window.wcPigglyPix.ajax_url}`, postData);

		try
		{
			const { data } = await axios.post(window.wcPigglyPix.ajax_url, qs.stringify({
				action: 'pgly_wc_piggly_pix_set_plugin_settings',
				section: section,
				data: JSON.stringify(postData),
				xSecurity: window.wcPigglyPix.x_security
			})); 

			if ( !data.success )
			{ throw new Error(data.data.message); }

			return true;
		}
		catch ( err: any )
		{ 
			console.log(err);
			throw err.response.data.data ?? err; 
		}
	},

	process : async () : Promise<string> => {
		console.log(`POST pgly_wc_piggly_pix_admin_cron_process ${window.wcPigglyPix.ajax_url}`);

		try
		{
			const { data } = await axios.post(window.wcPigglyPix.ajax_url, qs.stringify({
				action: 'pgly_wc_piggly_pix_admin_cron_process',
				xSecurity: window.wcPigglyPix.x_security
			})); 

			if ( !data.success )
			{ throw new Error(data.data.message); }

			return data.data.message;
		}
		catch ( err )
		{ 
			console.error(err);
			throw err; 
		}
	},

	recreateCron : async () : Promise<string> => {
		console.log(`POST pgly_wc_piggly_pix_admin_cron_recreate ${window.wcPigglyPix.ajax_url}`);

		try
		{
			const { data } = await axios.post(window.wcPigglyPix.ajax_url, qs.stringify({
				action: 'pgly_wc_piggly_pix_admin_cron_recreate',
				xSecurity: window.wcPigglyPix.x_security
			})); 

			if ( !data.success )
			{ throw new Error(data.data.message); }

			return data.data.message;
		}
		catch ( err )
		{ 
			console.error(err);
			throw err; 
		}
	}
}