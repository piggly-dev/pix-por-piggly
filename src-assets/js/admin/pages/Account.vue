<template>
	<h1 class="pgly-wps--title">Conta Pix</h1>

	<pgly-row>
		<pgly-column>
			<pgly-basic-input
				id="store_name"
				label="Nome da Loja"
				placeholder="Preencha com o nome da loja..."
				:required="true"
				:error="fields.store_name.error"
				v-model="fields.store_name.value">
				<template v-slot:description>
					Informe o nome da loja. São aceitos os caracteres: 
					<code>A-Z</code>, <code>a-z</code>, <code>0-9</code> e <code>espaço</code>.
				</template>
			</pgly-basic-input>

			<pgly-notification v-if="store_name_validator.length > 0" color="danger">
				O <strong>Nome da Loja</strong> {{store_name_validator}}.
				Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
				por tanto, corrigir o problema.
			</pgly-notification>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column :size="4">
			<pgly-basic-input
				id="bank"
				type="number"
				label="Código do Banco"
				:required="true"
				placeholder="Código do Banco"
				:error="fields.bank.error"
				v-model="fields.bank.value"
				@afterChange="validateBankCode">
				<template v-slot:description>
					Informe o código do banco para seleção rápida.
				</template>
			</pgly-basic-input>
		</pgly-column>
		<pgly-column :size="8">
			<pgly-basic-select
				id="bank"
				label="Banco"
				placeholder="Nenhum banco selecionado..."
				:options="fields.bank.options"
				:error="fields.bank.error"
				v-model="fields.bank.value">
			</pgly-basic-select>
		</pgly-column>
	</pgly-row>
	
			
	<pgly-notification color="warning">
		O banco não é obrigatório e deve apesar ser considerado
		<strong>apenas</strong> quando alguma API do Pix estiver
		conectada a este plugin. Saiba mais em <strong>API</strong> no menu "Pix por Piggly".
	</pgly-notification>

	<pgly-row>
		<pgly-column :size="6">
			<pgly-basic-input
				id="merchant_name"
				label="Nome do Titular da Conta"
				:required="true"
				placeholder="Preencha com o nome do titular..."
				:error="fields.merchant_name.error"
				v-model="fields.merchant_name.value">
				<template v-slot:description>
					Informe o nome do titular da conta que irá receber o PIX. 
					São aceitos os caracteres: <code>A-Z</code>, <code>a-z</code> 
					e <code>espaço</code>.
				</template>
			</pgly-basic-input>

			<pgly-notification v-if="merchant_name_validator.length > 0" color="danger">
				O <strong>Nome do Titular</strong> {{merchant_name_validator}}.
				Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
				por tanto, corrigir o problema.
			</pgly-notification>
		</pgly-column>
		<pgly-column :size="6">
			<pgly-basic-input
				id="merchant_city"
				label="Cidade do Titular da Conta"
				:required="true"
				placeholder="Preencha com a chave privada..."
				:error="fields.merchant_city.error"
				v-model="fields.merchant_city.value">
				<template v-slot:description>
					Preencha a cidade do titular da conta, sem acentos e em maiúsculo.
				</template>
			</pgly-basic-input>

			<pgly-notification v-if="merchant_city_validator.length > 0" color="danger">
				A <strong>Cidade do Titular</strong> {{merchant_city_validator}}.
				Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
				por tanto, corrigir o problema.
			</pgly-notification>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column :size="6">
			<pgly-basic-select
				id="key_type"
				label="Tipo da Chave Pix"
				placeholder="Selecione um tipo de chave..."
				:options="fields.key_type.options"
				:error="fields.key_type.error"
				v-model="fields.key_type.value"
				@afterChange="changedPixKeyType">
				<template v-slot:description>
					Informe o tipo da chave pix que será utilizada.
				</template>
			</pgly-basic-select>
		</pgly-column>
		<pgly-column :size="6">
			<pgly-basic-input
				id="key_value"
				label="Chave Pix"
				placeholder="Preencha com a chave pix..."
				:required="true"
				:error="fields.key_value.error"
				v-model="fields.key_value.value"
				@afterChange="validatePixKey">
				<template v-slot:description>
					Digite sua chave pix como ela foi cadastrada.
				</template>
			</pgly-basic-input>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column>
			<pgly-basic-input
				id="description"
				label="Descrição do Pix"
				placeholder="Preencha com a descrição do pix..."
				:required="true"
				:error="fields.description.error"
				v-model="fields.description.value">
				<template v-slot:description>
					Evite acentos, alguns bancos recusam acentos.
				</template>
			</pgly-basic-input>

			<pgly-notification v-if="description_validator.length > 0" color="danger">
				A <strong>descrição</strong> {{description_validator}}.
				Isso pode acarretar problemas de leitura do Pix em alguns bancos. Considere,
				por tanto, corrigir o problema.
			</pgly-notification>
			
			<pgly-notification :light="true" color="primary">
				<h4 class="pgly-wps--title pgly-wps-is-7">Merge Tags</h4>
				<p><code v-pre>{{order_number}}</code> - Número do pedido.</p>
				<p><code v-pre>{{store_name}}</code> - Nome da loja.</p>
			</pgly-notification>
		</pgly-column>
	</pgly-row>

	<pgly-row>
		<pgly-column>
			<pgly-basic-checkbox
				id="fix"
				label="Modo Auto-Correção"
				placeholder="Habilite para auto corrigir dados inválidos"
				:error="fields.fix.error"
				v-model="fields.fix.value"
				@afterChange="onChanged">
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
import BankOptions from "@/core/banks.json";
import { fieldsSetError } from '@/core/global';

import {
	PglyAsyncButton,
	PglyBasicCheckbox,
	PglyBasicInput,
	PglyBasicSelect,
	PglyNotification,
	PglyRow,
	PglyColumn
} from '@piggly/vue-pgly-wps-settings';

import { IErrorInput, IField } from "@piggly/vue-pgly-wps-settings/dist/types/src/core/interfaces";
import { CronFrequencyOptions } from "@/core/data";
import { __asyncValues } from "tslib";

export default defineComponent({
	name: 'account',
	
	components: {
		PglyAsyncButton,
		PglyBasicCheckbox,
		PglyBasicInput,
		PglyBasicSelect,
		PglyNotification,
		PglyRow,
		PglyColumn
	},

	data () {
		return {
			fields: {
				store_name: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('account').get('store_name', ''),
				},
				bank: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('account').get('bank', '').toString(),
					options: BankOptions
				},
				merchant_name: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('account').get('merchant_name', ''),
				},
				merchant_city: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('account').get('merchant_city', ''),
				},
				key_type: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('account').get('key_type', ''),
					options: [
						{ value: 'random', label: 'Chave Aleatória' },
						{ value: 'document', label: 'CPF/CNPJ' },
						{ value: 'email', label: 'E-mail' },
						{ value: 'phone', label: 'Telefone' }
					]
				},
				key_value: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('account').get('key_value', '')
				},
				description: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('account').get('description', ''),
					options: CronFrequencyOptions
				},
				fix: {
					error: {state: false} as IErrorInput,
					value: store.state.settings.get('account').get('fix', false),
				},
			} as { [key: string]: IField }
		}
	},

	computed: {
		store_name_validator () : string {
			return this.validatePixField(this.fields.store_name.value);
		},
		
		merchant_name_validator () : string {
			return this.validatePixField(this.fields.merchant_name.value);
		},
		
		merchant_city_validator () : string {
			return this.validatePixField(this.fields.merchant_city.value);
		},
		
		description_validator () : string {
			return this.validatePixField(this.fields.description.value, 40);
		}
	},

	methods: {
		onChanged () : void {
			if ( !store.state.editing )
			{ store.commit.CHANGE_EDIT_STATE(true); }
		},

		async submit () : Promise<boolean> {
			if ( this.fields.merchant_name.value.length === 0 )
			{ fieldsSetError(this.fields, 'merchant_name', 'Preencha o Nome do Titular'); }
			
			if ( this.fields.merchant_city.value.length === 0 )
			{ fieldsSetError(this.fields, 'merchant_city', 'Preencha a Cidade do Titular'); }

			if ( this.fields.key_value.value.length === 0 )
			{ fieldsSetError(this.fields, 'key_value', 'Preencha a Chave Pix'); }

			await api.saveSettings('account', {
				store_name: this.fields.store_name.value,
				bank: this.fields.bank.value,
				merchant_name: this.fields.merchant_name.value,
				merchant_city: this.fields.merchant_city.value,
				key_type: this.fields.key_type.value,
				key_value: this.fields.key_value.value,
				description: this.fields.description.value,
				fix: this.fields.fix.value
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
		},

		validatePixField ( val: string, length: number = 25 ) : string {			
			const msgs  = [];

			if ( val.length > length )
			{ msgs.push(`possui mais de ${length} caracteres`); }

			if ( val.replace(/[A-Za-z0-9\s\{\}\_\*]*/i, '').length !== 0 )
			{ msgs.push('possui caracteres inválidos'); }

			return msgs.join(', ');
		},

		validateBankCode ( val: string ) {
			this.fields.bank.value = parseInt(val).toString();
		},

		changedPixKeyType ( val: string )
		{
			this.validatePixKey(this.fields.key_value.value);
		},

		validatePixKey ( val: string ) {
			const type = this.fields.key_type.value;
			this.fields.key_value.error = { state: false };

			if ( type === 'random' )
			{
				if ( !val.match(/^[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/i) )
				{ this.fields.key_value.error = { state: true, message: 'Chave aleatória inválida' }; }

				return;
			}

			if ( type === 'email' )
			{
				if ( !val.match(/^.+@.+\.[^.]+$/i) )
				{ this.fields.key_value.error = { state: true, message: 'Não parece um e-mail válido' }; }

				return;
			}

			if ( type === 'phone' )
			{
				if ( !val.match(/^[\d\(\)\ \-]+$/i) || val.length < 10 )
				{ this.fields.key_value.error = { state: true, message: 'Não parece um telefone válido' }; }

				return;
			}

			if ( type === 'document' )
			{
				val = val.replace(/[^\d]+/g, '');

				if ( val.length <= 11 )
				{
					if ( !this.validateCPF(val) )
					{ this.fields.key_value.error = { state: true, message: 'Não parece um CPF/CNPJ válido' }; }
				
					this.fields.key_value.value = this.formatCPF(val);
				}
				else if ( val.length > 11 && val.length <= 14 )
				{
					if ( !this.validateCNPJ(val) )
					{ this.fields.key_value.error = { state: true, message: 'Não parece um CPF/CNPJ válido' }; }
				
					this.fields.key_value.value = this.formatCNPJ(val);
				}
				else
				{ this.fields.key_value.error = { state: true, message: 'Não parece um CPF/CNPJ válido' }; }
				
				return;
			}
		},

		formatCPF (val: string) : string {
			const pattern = '***.***.***-**';
			const chars = val.split('');

			let count = 0;
			let formatted = '';

			for ( let i=0; i < pattern.length; i++ ) 
			{
				const c = pattern[i];

				if (chars[count]) 
				{
					if (/\*/.test(c)) 
					{
						formatted += chars[count];
						count++;
					} 
					else 
					{ formatted += c; }
				} 
			}

			return formatted;
		},

		formatCNPJ (val: string) : string {
			const pattern = '**.***.***/****-**';

			const chars = val.split('');

			let count = 0;
			let formatted = '';

			for ( let i=0; i < pattern.length; i++ ) 
			{
				const c = pattern[i];

				if (chars[count]) 
				{
					if (/\*/.test(c)) 
					{
						formatted += chars[count];
						count++;
					} 
					else 
					{ formatted += c; }
				} 
			}

			return formatted;
		},

		validateCPF ( val: string ) : boolean {
			if (val.length !== 11 || !!val.match(/(\d)\1{10}/)) return false;

			let document = val.split('');
			const validator = document
				.filter((digit, index, array) => index >= array.length - 2 && digit)
				.map( el => +el );
			const toValidate = (pop: number) => document
				.filter((digit, index, array) => index < array.length - pop && digit)
				.map(el => +el);
			const rest = (count: number, pop: number) => (toValidate(pop)
				.reduce((soma, el, i) => soma + el * (count - i), 0) * 10) % 11 % 10;
			
			return !(rest(10,2) !== validator[0] || rest(11,1) !== validator[1]);
		},

		validateCNPJ ( val: string ) : boolean {
			if (val.length !== 14 || !!val.match(/(\d)\1{13}/)) return false;

			const b = [6,5,4,3,2,9,8,7,6,5,4,3,2];
			const d = val.split('').map(n=>parseInt(n));

			for(var i = 0, n = 0; i < 12; n += d[i] * b[++i]);

			if(d[12] != (((n %= 11) < 2) ? 0 : 11 - n)) return false;

			for(var i = 0, n = 0; i <= 12; n += d[i] * b[i++]);

			if(d[13] != (((n %= 11) < 2) ? 0 : 11 - n)) return false;

			return true;
		}
	}
});
</script>