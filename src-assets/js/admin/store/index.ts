import Payload from "@/core/models/payload";
import { IToast } from "@piggly/vue-pgly-wps-settings/dist/types/src/core/interfaces";
import { createDirectStore } from 'direct-vuex';

export interface State {
	editing: boolean,
	settings: Payload,
	toasts: Array<IToast>
}

export interface Settings {
	global: {
		debug: boolean,
		log_api: boolean
	},
	gateway: {
		enabled: boolean,
		title: string,
		description: string
	},
	blockchain: {
		address: '',
		latest_rate: number,
		runtime_rate: boolean,
		tx_limit: number,
		tx_confirmations: number,
		rate_frequency: string
	},
	transactions: {
		txid_prefix: string,
		txid_length: '',
		timeout: boolean,
		timeout_action: string,
		lifetime: number,
		tax: number,
		tx_frequency: string
	}
};

const {
	store,
	rootActionContext,
	moduleActionContext,
	rootGetterContext,
	moduleGetterContext
} = createDirectStore({
	state: {
		editing: false,
		settings: new Payload(),
		toasts: []
	} as State,
	mutations: {
		CHANGE_EDIT_STATE (state: State, editing: boolean ) {
			state.editing = editing;
		},
		LOAD_SETTINGS (state: State, settings: Settings) {
			state.settings = new Payload().import(settings);
		},
		ADD_TOAST (state: State, toast: IToast) {
			if ( !toast.id ) toast.id = state.toasts.length+1;
			state.toasts.push(toast);
		},
		REMOVE_TOAST (state: State, id: number) {
			state.toasts = state.toasts.filter((i: IToast) => {
				return i.id !== id;
			});
		}
	}
});

// Export the direct-store instead of the classic Vuex store.
export default store;

// The following exports will be used to enable types in the
// implementation of actions and getters.
export {
	rootActionContext,
	moduleActionContext,
	rootGetterContext,
	moduleGetterContext
 }

// export interface State {
// 	editing: boolean,
// 	settings: Payload,
// 	toasts: Array<IToast>
// }

// export const key: InjectionKey<Store<State>> = Symbol();

// export function useStore () {
// 	return baseUseStore(key);
// };

// export const store = createStore<State>({
// 	state: {
// 		editing: false,
// 		settings: new Payload(),
// 		toasts: []
// 	}
// });