import axios from "axios";

declare global {
	interface Window {
		wcPigglyPix: {
			ajax_url: string,
			x_security: string,
			plugin_url: string,
			assets_url: string
		}
	}
}

export default axios.create({
	timeout: 60000
});