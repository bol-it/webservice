<template>
    <div>
        <v-btn
            depressed
            :rounded="styles_btn.rounded"
            :color="disabled ? 'grey' : styles_btn.color"
            :dark="disabled ? false : styles_btn.dark"
            :outlined="styles_btn.outlined"
            :disabled="disabled"
            @click="submit()"
        >
            <v-icon left>mdi-content-save</v-icon>
            {{properties_btn.label}}
        </v-btn>
    </div>
</template>

<script>


export default {
    components: {

    },
    props: {
        mainpath: String,
        value_component: String,
        id_comp: Number,
        properties: Object,
        styles: Object,
        submitButton: Function,
        disabled: {
			type: Boolean,
			default: false,
		},
    },
    data: () => ({
        value_btn: '',
        properties_btn: {},
        styles_btn: {},
        rules: {
          required: value => !!value || 'Поле обязательное для ввода',
          not_required: value => true,
        },
    }),
    computed: {

    },
    watch: {

    },
    created () {
        this.value_btn = this.value_component;
        this.properties_btn = this.properties;
        this.styles_btn = this.styles;
        //console.log(this.properties_btn);
    },
    methods: {
        submit() {
            this.submitButton();
        },
    },
}
</script>