<template>
	<pgly-toaster
		:toasts="getToasts"
		@toastClose="onCloseToast"/>
	<pgly-navigator
		:items="this.navigator"
		:modelValue="this.currentTab"
		@update:modelValue="onChangedNavigator"/>

	<pgly-notification
		v-if="isEditing"
		color="warning">
		<strong>As configurações não foram salvas.</strong>
		Salve antes de continuar.
	</pgly-notification>
		
	<div 
		v-if="loading" 
		style="margin: 48px auto; display: table">
		<PglySpinner 
			color="primary"/>
	</div>
	<component 
		v-else 
		:is="currentTab">
	</component>

</template>

<script lang="ts">
import { defineComponent } from "@vue/runtime-core";

import { PglyNavigator, PglyNotification, PglySpinner, PglyToaster } from "@piggly/vue-pgly-wps-settings";
import { INavigatorItem, IToast } from "@piggly/vue-pgly-wps-settings/dist/types/src/core/interfaces";

import store from "@/store";
import api from "@/api/api";

import Global from "@/pages/Global.vue";
import Account from "@/pages/Account.vue";
import Orders from "@/pages/Orders.vue";

export default defineComponent({
	name: 'App',

	components: {
		PglyNavigator,
		PglyNotification,
		PglySpinner,
		PglyToaster,
		Global,
		Account,
		Orders
	},

	created () {
		this.getPluginSettings();
	},

	data () {
		return {
			loading: true,
			currentTab: 'global',
			navigator: [ 
				{
					key: 'global',
					label: 'Principal'
				}, 
				{
					key: 'account',
					label: 'Conta Pix'
				}, 
				{
					key: 'orders',
					label: 'Pedidos'
				}
			] as Array<INavigatorItem>
		}
	},

	computed: {
		getToasts () : Array<IToast> {
			return store.state.toasts;
		},

		isEditing () : boolean {
			return store.state.editing;
		}
	},

	methods: {
		async getPluginSettings () : Promise<void> {
			this.loading = true;

			try
			{ 
				store.commit.LOAD_SETTINGS(await api.getSettings());
				this.loading = false;
			}
			catch ( err )
			{ 
				console.error(err);

				store.commit.ADD_TOAST({
					timer: 4000,
					body: 'Não foi possível carregar as configurações...',
					color: 'danger'
				});
			}
		},

		onCloseToast ( id: number ) : void {
			store.commit.REMOVE_TOAST(id);
		},

		onChangedNavigator ( key: string ) : void {
			if ( this.isEditing )
			{
				store.commit.ADD_TOAST({
					timer: 4000,
					body: 'Salve antes de continuar...',
					color: 'warning'
				});

				return;
			}

			this.currentTab = key;
		}
	}
});
</script>