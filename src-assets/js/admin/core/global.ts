import { IField } from "@piggly/vue-pgly-wps-settings/dist/types/src/core/interfaces";

export const fieldsSetError = ( fields: { [key: string]: IField }, name: string, message: string ) => {
	if ( name in fields )
	{ fields[name].error = { state: true, message: message }; } 

	throw new Error('Verifique os campos antes de enviar');
};

export const fieldsHasError = ( fields: { [key: string]: IField } ) : boolean => {
	Object.keys(fields).forEach(k => {
		if ( fields[k].error.state )
		{ return true; }
	});

	return false;
};

export const fieldsFlushErrors = ( fields: { [key: string]: IField } ) => {
	Object.keys(fields).forEach(k => {
		fields[k].error.state = false;
	});
};
