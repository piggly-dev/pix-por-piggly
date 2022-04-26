<template>
	<h1 class="pgly-wps--title">Processamento do Pix</h1>

	<pgly-row>
		<pgly-column>
			<pgly-notification color="danger">
				O pagamento do Pix não é verificado automaticamente
				para os Pix do tipo "Verificação Manual (Estático)".
				Para atualizar o Pix automaticamente, é necessário
				ter uma API do Pix conectada ao plugin.
				Saiba mais em <strong>API do Pix</strong> no menu 
				lateral "Pix por Piggly".
			</pgly-notification>

			<pgly-notification color="warning">
				As atividades de processamento do Pix aplicam uma rotina
				de eventos no Wordpress que faz a verificação individual
				dos Pix. Nesta verificação, é verificado se o Pix será
				cancelado, atualizado ou removido.
			</pgly-notification>
		</pgly-column>
	</pgly-row>

	<div class="pgly-wps--space"></div>
	<h2 class="pgly-wps--title pgly-wps-is-6">Processamento</h2>

	<pgly-row>
		<pgly-column>
			<pgly-basic-select
				id="cron_frequency"
				label="Período de Processamento dos Pix"
				:options="fields.cron_frequency.options"
				:error="fields.cron_frequency.error"
				v-model="fields.cron_frequency.value">
				<template v-slot:description>
					Frequência na qual os Pix serão processados.
					Essa tarefa irá expirar os Pix, cancelar os pedidos
					ou marcar os Pix como pagos quando houver uma
					API Pix conectada ao plugin.
				</template>
			</pgly-basic-select>
		</pgly-column>
	</pgly-row>
	
	<div class="pgly-wps--space"></div>

	<pgly-row>
		<pgly-column>
			<pgly-notification color="info">
				Se o Pix não estiver cancelando ou atualizando automaticamente
				verifique o status de atividade da cronjob do Pix abaixo. Se
				for equivalente a "Inativo", pressione o botão "Recriar Cronjob".
			</pgly-notification>

			<pgly-notification :color="cron_status ? 'success' : 'danger'">
				Status de operação da cronjob: 
				<strong>
					<span v-if="cron_status">Ativo</span>
					<span v-else>Inativo</span>
				</strong>
			</pgly-notification>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column>
			<pgly-async-button 
				label="Processar Agora" 
				type="expanded"
				color="primary"
				:action="doTransactions"
				@buttonLoaded="doTransactionsSubmitted"
				@buttonError="doTransactionsError" />
			<p>
				Força o processamento dos Pix imediatamente.
			</p>
		</pgly-column>
		<pgly-column>
			<pgly-async-button 
				label="Recriar Cronjob" 
				type="expanded"
				color="white"
				:action="doCron"
				@buttonLoaded="doCronSubmitted"
				@buttonError="doCronError" />
			<p>
				Recria o evento de processamento dos Pix no Wordpress.
			</p>
		</pgly-column>
	</pgly-row>

	<div class="pgly-wps--space"></div>

	<pgly-async-button 
		label="Salvar Alterações" 
		color="accent"
		:action="submit"
		@buttonLoaded="submitted" 
		@buttonError="notSubmitted"/>
</template>

<script lang="ts">
import { defineComponent } from "@vue/runtime-core";

import store from '@/store';
import api from "@/api/api";
import { fieldsSetError } from '@/core/global';

import {
	PglyAsyncButton,
	PglyLinkButton,
	PglyBasicCheckbox,
	PglyBasicInput,
	PglyBasicSelect,
	PglyNotification,
	PglyRow,
	PglyColumn
} from '@piggly/vue-pgly-wps-settings';

import { IErrorInput, IField } from "@piggly/vue-pgly-wps-settings/dist/types/src/core/interfaces";
import { CronFrequencyOptions } from "@/core/data";

export default defineComponent({
	name: 'orders',
	
	components: {
		PglyAsyncButton,
		PglyLinkButton,
		PglyBasicCheckbox,
		PglyBasicInput,
		PglyBasicSelect,
		PglyNotification,
		PglyRow,
		PglyColumn
	},

	data () {
		return {
			window: window,
			cron_status: store.state.settings.get('runtime').get('cron_status', true),
			fields: {
				cron_frequency: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('orders').get('cron_frequency', ''),
					options: CronFrequencyOptions
				},		
			} as { [key: string]: IField }
		}
	},

	methods: {
		onChanged () : void {
			if ( !store.state.editing )
			{ store.commit.CHANGE_EDIT_STATE(true); }
		},

		async submit () : Promise<boolean> {
			await api.saveSettings('processing', {
				cron_frequency: this.fields.cron_frequency.value
			});

			return true;
		},

		submitted (response: boolean) : void {
			if ( response )
			{
				store.commit.ADD_TOAST({
					body: 'Configurações salvas com sucesso',
					color: 'success',
					timer: 4000
				});

				store.commit.CHANGE_EDIT_STATE(false);
				this.getPluginSettings();
			}
		},

		notSubmitted ( err: Error ) : void {
			store.commit.ADD_TOAST({
				body: err.message || 'Não foi possível salvar as configurações',
				color: 'danger',
				timer: 4000
			});
		},

		async doTransactions () : Promise<string> {
			return await api.process();
		},

		doTransactionsSubmitted (response: string) : void {
			store.commit.ADD_TOAST({
				body: 'Pix processados',
				color: 'success',
				timer: 4000
			});
		},

		doTransactionsError () : void {
			store.commit.ADD_TOAST({
				body: 'Não foi possível processar os pix',
				color: 'danger',
				timer: 4000
			});
		},

		async doCron () : Promise<string> {
			return await api.recreateCron();
		},

		doCronSubmitted (response: string) : void {
			store.commit.ADD_TOAST({
				body: 'Cronjob recriada com sucesso',
				color: 'success',
				timer: 4000
			});
		},

		doCronError () : void {
			store.commit.ADD_TOAST({
				body: 'Não foi possível recriar a cronjob',
				color: 'danger',
				timer: 4000
			});
		},

		async getPluginSettings () : Promise<void> {
			try
			{ store.commit.LOAD_SETTINGS(await api.getSettings()); }
			catch ( err )
			{ 
				console.error(err);

				store.commit.ADD_TOAST({
					timer: 4000,
					body: 'Não foi possível carregar as configurações...',
					color: 'danger'
				});
			}
		}
	}
});
</script>