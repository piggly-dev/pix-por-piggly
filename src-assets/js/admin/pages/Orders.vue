<template>
	<h1 class="pgly-wps--title">Pedidos</h1>

	<h2 class="pgly-wps--title pgly-wps-is-6">Status dos Pedidos</h2>
	
	<pgly-row>
		<pgly-column>
			<pgly-basic-checkbox
				id="decrease_stock"
				label="Reduzir Estoque ao criar o Pix"
				placeholder="O estoque será reduzido assim que o Pix for criado."
				:error="fields.decrease_stock.error"
				v-model="fields.decrease_stock.value"
				@afterChange="onChanged">
			</pgly-basic-checkbox>
		</pgly-column>
	</pgly-row>

	<pgly-notification color="warning">
		Quando o status para Aguardando Pagamento for <code>
		Pendente</code> a página de redirecionamento do pedido
		será a página de pagamento do pedido, se for escolhido
		qualquer outro status, então a página de redirecionamento
		do pedido será a página "Obrigado".
	</pgly-notification>

	<pgly-row>
		<pgly-column>
			<pgly-basic-select
				id="waiting_status"
				label="Status para Aguardando Pagamento"
				placeholder="Selecione um status..."
				:options="fields.waiting_status.options"
				:error="fields.waiting_status.error"
				v-model="fields.waiting_status.value"
				@afterChange="onChanged">
				<template v-slot:description>
					Recomendamos o status Pendente (<code>pendente</code>)
					quando utilizando uma API do Pix. Para Pix Estáticos,
					selecione o status desejado.
				</template>
			</pgly-basic-select>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column :size="6">
			<pgly-basic-select
				id="receipt_status"
				label="Status para Comprovante Pix Recebido"
				placeholder="Selecione um status..."
				:options="fields.receipt_status.options"
				:error="fields.receipt_status.error"
				v-model="fields.receipt_status.value"
				@afterChange="onChanged">
				<template v-slot:description>
					Recomendamos o status Aguardando Pagamento (<code>on-hold</code>).
				</template>
			</pgly-basic-select>
		</pgly-column>
		<pgly-column :size="6">
			<pgly-basic-select
				id="paid_status"
				label="Status para Pix Pago"
				placeholder="Selecione um status..."
				:options="fields.paid_status.options"
				:error="fields.paid_status.error"
				v-model="fields.paid_status.value"
				@afterChange="onChanged">
				<template v-slot:description>
					Para pedidos com produtos não virtuais que possuem
					gerenciamento de estoque.
				</template>
			</pgly-basic-select>
		</pgly-column>
	</pgly-row>

	<div class="pgly-wps--space"></div>
	<h2 class="pgly-wps--title pgly-wps-is-6">Expiração do Pix</h2>

	<pgly-row>
		<pgly-column>
			<pgly-basic-checkbox
				id="cancel_when_expired"
				label="Cancelar Pedido após Expiração"
				placeholder="Cancela o pedido, quando possível, após a expiração do Pix."
				:error="fields.cancel_when_expired.error"
				v-model="fields.cancel_when_expired.value"
				@afterChange="onChanged">
			</pgly-basic-checkbox>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column>
			<pgly-basic-checkbox
				id="show_expiration"
				label="Exibir Data de Expiração para o cliente"
				placeholder="Mostra um aviso que o Pix irá expirar na data especificada."
				:error="fields.show_expiration.error"
				v-model="fields.show_expiration.value"
				@afterChange="onChanged">
			</pgly-basic-checkbox>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column :size="6">
			<pgly-basic-input
				id="expires_after"
				label="Tempo de Expiração"
				tag="Horas"
				:required="true"
				placeholder="Preencha o tempo de expiração..."
				:error="fields.expires_after.error"
				v-model="fields.expires_after.value">
				<template v-slot:description>
					O tempo de expiração do Pix, em horas, quando
					o status do pedido for "Aguardando Pagamento",
					sem comprovante enviado. Mantenha o valor <code>0</code>
					para desativar.
				</template>
			</pgly-basic-input>
		</pgly-column>
		<pgly-column :size="6">
			<pgly-basic-input
				id="closest_lifetime"
				label="Notificar quando próximo a data da expiração"
				type="number"
				tag="Minutos antes"
				:required="true"
				placeholder="Informe o tamanho..."
				:error="fields.closest_lifetime.error"
				v-model="fields.closest_lifetime.value">
				<template v-slot:description>
					Tempo, em minutos, antes da expiração para
					notificar o usuário que o pagamento será expirado.
					Mantenha o valor <code>0</code> para desativar.
				</template>
			</pgly-basic-input>
		</pgly-column>
	</pgly-row>
	
	<div class="pgly-wps--space"></div>
	<h2 class="pgly-wps--title pgly-wps-is-6">Desconto</h2>

	<pgly-row>
		<pgly-column :size="4">
			<pgly-basic-input
				id="discount_value"
				type="number"
				label="Valor do Desconto"
				:tag="fields.discount_type.value === 'PERCENT' ? '%' : 'R$'"
				:required="true"
				placeholder="Informe o valor do desconto..."
				:error="fields.discount_value.error"
				v-model="fields.discount_value.value">
			</pgly-basic-input>
		</pgly-column>
		<pgly-column :size="4">
			<pgly-basic-select
				id="discount_type"
				label="Tipo de Desconto"
				:options="fields.discount_type.options"
				:error="fields.discount_type.error"
				v-model="fields.discount_type.value">
			</pgly-basic-select>
		</pgly-column>
		<pgly-column :size="4">
			<pgly-basic-input
				id="discount_label"
				label="Rótulo do Desconto"
				tag="Exibido no Carrinho"
				:required="true"
				placeholder="Informe o rótulo do desconto..."
				:error="fields.discount_label.error"
				v-model="fields.discount_label.value">
			</pgly-basic-input>
		</pgly-column>
	</pgly-row>
	
	<div class="pgly-wps--space"></div>
	<h2 class="pgly-wps--title pgly-wps-is-6">Comprovante Pix</h2>
	
	<pgly-row>
		<pgly-column>
			<pgly-basic-checkbox
				id="receipt_page"
				label="Habilitar Comprovante via Web"
				placeholder="O usuário poderá acessar uma página restrita para envio do comprovante."
				:error="fields.receipt_page.error"
				v-model="fields.receipt_page.value"
				@afterChange="onChanged">
			</pgly-basic-checkbox>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column>
			<pgly-basic-select
				id="shows_receipt"
				label="Posição dos Links do Comprovante"
				placeholder="Selecione uma posição..."
				:options="fields.shows_receipt.options"
				:error="fields.shows_receipt.error"
				v-model="fields.shows_receipt.value"
				@afterChange="onChanged">
				<template v-slot:description>
					Recomendamos o status Aguardando Pagamento (<code>on-hold</code>).
				</template>
			</pgly-basic-select>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column>
			<pgly-basic-select
				id="after_receipt"
				label="Página de Redirecionamento"
				placeholder="Selecione uma página..."
				:options="fields.after_receipt.options"
				:error="fields.after_receipt.error"
				v-model="fields.after_receipt.value"
				@afterChange="onChanged">
				<template v-slot:description>
					Página para redirecionar o usuário após envio
					do comprovante. A página não será exibida
					para clientes anônimos por razões de segurança
					e prevenção contra SPAM e invasões.
				</template>
			</pgly-basic-select>
		</pgly-column>
	</pgly-row>

	<div class="pgly-wps--space"></div>
	<h3 class="pgly-wps--title pgly-wps-is-7">Whatsapp</h3>

	<pgly-row>
		<pgly-column :size="6">
			<pgly-basic-input
				id="whatsapp_number"
				label="Número do Whatsapp"
				:required="true"
				placeholder="Preencha o número completo..."
				:error="fields.whatsapp_number.error"
				v-model="fields.whatsapp_number.value">
				<template v-slot:description>
					Preencha o número do Whatsapp, incluindo o DDD.
				</template>
			</pgly-basic-input>
		</pgly-column>
		<pgly-column :size="6">
			<pgly-basic-input
				id="whatsapp_message"
				label="Mensagem Padrão"
				:required="true"
				placeholder="Preencha a mensagem padrão..."
				:error="fields.whatsapp_message.error"
				v-model="fields.whatsapp_message.value">
				<template v-slot:description>
					Mensagem que aparecerá para o cliente enviar.
				</template>
			</pgly-basic-input>
		</pgly-column>
	</pgly-row>

	<div class="pgly-wps--space"></div>
	<h3 class="pgly-wps--title pgly-wps-is-7">Telegram</h3>

	<pgly-row>
		<pgly-column :size="6">
			<pgly-basic-input
				id="telegram_number"
				label="Número do Telegram"
				:required="true"
				placeholder="Preencha o usuário do telegram..."
				:error="fields.telegram_number.error"
				v-model="fields.telegram_number.value">
				<template v-slot:description>
					Preencha o usuário do Telegram.
				</template>
			</pgly-basic-input>
		</pgly-column>
		<pgly-column :size="6">
			<pgly-basic-input
				id="telegram_message"
				label="Mensagem Padrão"
				:required="true"
				placeholder="Preencha a mensagem padrão..."
				:error="fields.telegram_message.error"
				v-model="fields.telegram_message.value">
				<template v-slot:description>
					Mensagem que aparecerá para o cliente enviar.
				</template>
			</pgly-basic-input>
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
			fields: {
				decrease_stock: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('orders').get('decrease_stock', true),
				},
				waiting_status: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('orders').get('waiting_status', ''),
					options: store.state.settings.get('runtime').get('statuses')
				},
				receipt_status: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('orders').get('receipt_status', ''),
					options: store.state.settings.get('runtime').get('statuses')
				},
				paid_status: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('orders').get('paid_status', ''),
					options: store.state.settings.get('runtime').get('statuses')
				},
				cancel_when_expired: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('orders').get('cancel_when_expired', false),
				},
				show_expiration: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('orders').get('show_expiration', false),
				},
				expires_after: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('orders').get('expires_after', '').toString(),
				},
				closest_lifetime: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('orders').get('closest_lifetime', '').toString(),
				},
				discount_value: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('discount').get('value', ''),
				},
				discount_type: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('discount').get('type', ''),
					options: [
						{ value: "PERCENT", label: "Porcentagem" },
						{ value: "FIXED", label: "Valor Fixo" }
					]
				},
				discount_label: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('discount').get('label', ''),
				},
				after_receipt: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('orders').get('after_receipt', 0).toString(),
					options: store.state.settings.get('runtime').get('pages')
				},
				shows_receipt: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('receipts').get('shows_receipt', 'up'),
					options: [
						{ value: 'up', label: 'Acima do Pix' },
						{ value: 'down', label: 'Abaixo do Pix' }
					]
				},
				receipt_page: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('receipts').get('receipt_page', true),
				},
				whatsapp_number: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('receipts').get('whatsapp_number', ''),
				},
				whatsapp_message: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('receipts').get('whatsapp_message', ''),
				},
				telegram_number: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('receipts').get('telegram_number', ''),
				},
				telegram_message: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('receipts').get('telegram_message', ''),
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
			if ( this.fields.expires_after.value.length === 0 )
			{ this.fields.expires_after.value = '0'; }

			if ( this.fields.closest_lifetime.value.length === 0 )
			{ this.fields.closest_lifetime.value = '0'; }

			await api.saveSettings('discount', {
				value: this.fields.discount_value.value,
				type: this.fields.discount_type.value,
				label: this.fields.discount_label.value,
			});

			await api.saveSettings('receipts', {
				shows_receipt: this.fields.shows_receipt.value,
				receipt_page: this.fields.receipt_page.value,
				whatsapp_number: this.fields.whatsapp_number.value,
				whatsapp_message: this.fields.whatsapp_message.value,
				telegram_number: this.fields.telegram_number.value,
				telegram_message: this.fields.telegram_message.value
			});

			await api.saveSettings('orders', {
				decrease_stock: this.fields.decrease_stock.value,
				waiting_status: this.fields.waiting_status.value,
				receipt_status: this.fields.receipt_status.value,
				paid_status: this.fields.paid_status.value,
				after_receipt: this.fields.after_receipt.value,
				cancel_when_expired: this.fields.cancel_when_expired.value,
				show_expiration: this.fields.show_expiration.value,
				expires_after: this.fields.expires_after.value,
				closest_lifetime: this.fields.closest_lifetime.value
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