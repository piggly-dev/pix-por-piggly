<template>
	<pgly-row>
		<pgly-column>
			<pgly-notification color="danger">
				Se as páginas de pagamento do Pix e envio de comprovante 
				não estiverem sendo exibidas, não esqueça de atualizar 
				os <code>Links Permanentes</code> do Wordpress. 
				Basta acessar <code>Configurações > Links permanentes</code> 
				e salvar.
			</pgly-notification>

			<pgly-notification color="warning">
				<strong>Enfrentando algum problema?</strong> Visite a seção de 
				<strong>Suporte</strong> no menu "Pix por Piggly" e 
				resolva os principais problemas do plugin.
			</pgly-notification>
			
			<pgly-notification color="success">
				<strong>Gostou o bastante? 👇</strong> 
				Se você apreciar a função deste plugin e quiser apoiar 
				este trabalho para que continuemos atualizando, sinta-se
				livre para fazer qualquer doação para a chave aleatória Pix 
				<code>aae2196f-5f93-46e4-89e6-73bf4138427b</code> ❤.
			</pgly-notification>

			<pgly-link-button
				label="Avaliar o Plugin"
				color="primary"
				target="_blank"
				link="https://wordpress.org/plugins/pix-por-piggly/#reviews"/>
		</pgly-column>
	</pgly-row>

	<div class="pgly-wps--space"></div>
	<h1 class="pgly-wps--title">Configurações Gerais</h1>

	<pgly-row v-if="can_enable">
		<pgly-column>
			<pgly-basic-checkbox
				id="enabled"
				label="Ativar o método de pagamento"
				placeholder="Habilite o método de pagamento via Pix e começe a receber"
				:error="fields.enabled.error"
				v-model="fields.enabled.value"
				@afterChange="onChanged">
			</pgly-basic-checkbox>
		</pgly-column>
	</pgly-row>
	<pgly-notification v-else color="danger">
		Antes de habilitar o plugin, configure os dados
		da usa Conta Pix.
	</pgly-notification>

	<pgly-row>
		<pgly-column>
			<img :src="`${window.wcPigglyPix.assets_url}/assets/images/${fields.icon.value}.png`" style="width: 54px; height: auto"/>
			<pgly-basic-select
				id="icon"
				label="Ícone"
				:options="fields.icon.options"
				:error="fields.icon.error"
				v-model="fields.icon.value">
			</pgly-basic-select>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column :size="6">
			<pgly-basic-input
				id="title"
				label="Título do Método"
				:required="true"
				placeholder="Preencha o título..."
				:error="fields.title.error"
				v-model="fields.title.value"
				@afterChange="onChanged">
				<template v-slot:description>
					O título é exibido para o cliente durante o checkout.
				</template>
			</pgly-basic-input>
		</pgly-column>
		<pgly-column :size="6">
			<pgly-basic-input
				id="description"
				label="Descrição do Método"
				:required="true"
				placeholder="Preencha a descrição..."
				:error="fields.description.error"
				v-model="fields.description.value"
				@afterChange="onChanged">
				<template v-slot:description>
					A descrição é exibida para o cliente durante o checkout.
				</template>
			</pgly-basic-input>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column>
			<pgly-basic-input
				id="instructions"
				label="Instruções do Método"
				:required="true"
				placeholder="Preencha as instruções..."
				:error="fields.instructions.error"
				v-model="fields.instructions.value"
				@afterChange="onChanged">
				<template v-slot:description>
					Instruções básicas de pagamento via Pix.
				</template>
			</pgly-basic-input>
			
			<pgly-notification :light="true" color="primary">
				<h4 class="pgly-wps--title pgly-wps-is-7">Merge Tags</h4>
				<p><code v-pre>{{order_number}}</code> - Número do pedido.</p>
			</pgly-notification>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column>
			<pgly-basic-checkbox
				id="advanced_description"
				label="Ativar a Descrição Avançada"
				placeholder="Habilite a descrição avançada com instruções de pagamento Pix"
				:error="fields.advanced_description.error"
				v-model="fields.advanced_description.value"
				@afterChange="onChanged">
				<template v-slot:description>
					Por padrão, a descrição avançada apresenta os três passos para pagamento via Pix.
				</template>
			</pgly-basic-checkbox>

			<pgly-notification color="primary" :light="true">
				<strong>Quer editar o template?</strong> Copie o arquivo
				disponível em <code>/path/to/wp-content/plugins/pix-por-piggly/templates/woocommerce/html-woocommerce-instructions.php</code>
				para a pasta <code>/woocommerce</code> dentro da pasta do seu Tema.
			</pgly-notification>
		</pgly-column>
	</pgly-row>

	<div class="pgly-wps--space"></div>
	<h2 class="pgly-wps--title pgly-wps-is-6">Pagamento</h2>

	<pgly-row>
		<pgly-column>
			<pgly-basic-checkbox
				id="shows_qrcode"
				label="Via QR Code"
				placeholder="Habilitar o pagamento do Pix via QR Code"
				:error="fields.shows_qrcode.error"
				v-model="fields.shows_qrcode.value"
				@afterChange="onChanged">
			</pgly-basic-checkbox>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column>
			<pgly-basic-checkbox
				id="shows_copypast"
				label="Via Pix Copia & Cola"
				placeholder="Habilitar o pagamento do Pix via Pix Copia & Cola"
				:error="fields.shows_copypast.error"
				v-model="fields.shows_copypast.value"
				@afterChange="onChanged">
			</pgly-basic-checkbox>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column>
			<pgly-basic-checkbox
				id="shows_manual"
				label="Via Manual"
				placeholder="Habilitar o pagamento do Pix com preenchimento manual"
				:error="fields.shows_manual.error"
				v-model="fields.shows_manual.value"
				@afterChange="onChanged">
			</pgly-basic-checkbox>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column>
			<pgly-basic-checkbox
				id="shows_amount"
				label="Mostrar Valor do Pix no Ínicio"
				placeholder="Habilitar a visualização do Valor do Pix acima dos dados Pix"
				:error="fields.shows_amount.error"
				v-model="fields.shows_amount.value"
				@afterChange="onChanged">
			</pgly-basic-checkbox>
		</pgly-column>
	</pgly-row>

	<div class="pgly-wps--space"></div>
	<h2 class="pgly-wps--title pgly-wps-is-6">Depuração</h2>

	<pgly-row>
		<pgly-column>
			<pgly-basic-checkbox
				id="debug"
				label="Modo Debug"
				placeholder="Habilitar o registro completo de erros, informações e avisos"
				:error="fields.debug.error"
				v-model="fields.debug.value"
				@afterChange="onChanged">
				<template v-slot:description>
					Utilize apenas para inspecionar erros ou processos. 
					Mensagens de log em excesso podem ser criadas quando ativado.
				</template>
			</pgly-basic-checkbox>
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

const IconOptions = [
	{ value: 'pix-payment-icon', label: 'Preto' },
	{ value: 'pix-payment-icon-green', label: 'Tradicional (Verde)' },
	{ value: 'pix-payment-icon-white', label: 'Branco' }
];

export default defineComponent({
	name: 'global',
	
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
			can_enable: store.state.settings.get('account').get('key_value', '').length !== 0,
			fields: {
				enabled: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('gateway').get('enabled', false),
				},
				icon: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('gateway').get('icon', 'pix-payment-icon'),
					options: IconOptions
				},
				title: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('gateway').get('title', ''),
				},
				description: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('gateway').get('description', ''),
				},
				instructions: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('gateway').get('instructions', ''),
				},
				advanced_description: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('gateway').get('advanced_description', false),
				},
				shows_amount: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('gateway').get('shows_amount', false),
				},
				shows_qrcode: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('gateway').get('shows_qrcode', false),
				},
				shows_copypast: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('gateway').get('shows_copypast', false),
				},
				shows_manual: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('gateway').get('shows_manual', false),
				},
				debug: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('global').get('debug', false),
				}
			} as { [key: string]: IField }
		}
	},

	methods: {
		onChanged () : void {
			if ( !store.state.editing )
			{ store.commit.CHANGE_EDIT_STATE(true); }
		},

		async submit () : Promise<boolean> {
			if ( this.fields.enabled.value
					&& !store.state.settings.get('account').get('key_value') )
			{
				this.fields.enabled.value = false;
				
				store.commit.ADD_TOAST({
					body: 'Preencha os dados da Conta Pix antes de habilitar o plugin',
					color: 'warning',
					timer: 4000
				});
			}

			if ( this.fields.title.value.length === 0 )
			{ fieldsSetError(this.fields, 'title', 'Preencha o título'); }

			await api.saveSettings('global', {
				debug: this.fields.debug.value
			});
			
			await api.saveSettings('gateway', {
				enabled: this.fields.enabled.value,
				title: this.fields.title.value,
				icon: this.fields.icon.value,
				description: this.fields.description.value,
				advanced_description: this.fields.advanced_description.value,
				instructions: this.fields.instructions.value,
				shows_qrcode: this.fields.shows_qrcode.value,
				shows_copypast: this.fields.shows_copypast.value,
				shows_manual: this.fields.shows_manual.value,
				shows_amount: this.fields.shows_amount.value
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