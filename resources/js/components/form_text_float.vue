<template>
    <div>
        <v-text-field
            :label="properties_text.label"
            :placeholder="properties_text.placeholder_availability ? properties_text.placeholder : ''"
            :hint="properties_text.hint_availability ? properties_text.hint : ''"
            :outlined="styles_text.type_style == 'outlined'"
            :filled="styles_text.type_style == 'filled'"
            :dense="styles_text.dense"
            :clearable="styles_text.clearable"
            :rounded="styles_text.rounded"
            :rules="properties_text.required ? [rules.required] : [rules.not_required]"
            :prepend-icon="properties_text.money ? 'mdi-cash' : 'mdi-surround-sound-2-1'"
            :suffix="properties_text.money ? '₽' : ''"
            maxlength="55" counter
            v-model="value_text"
            oncontextmenu='return false;'
            @paste="onPaste"
            v-filter="float_model2"
        ></v-text-field>
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
        properties: {
            type: Object,
            default: () => (
                {label: 'Надпись', placeholder_availability: false, placeholder: 'Текст-заполнитель для ввода', hint_availability: false, hint: 'Текст подсказки', required: true, float_text: { decimal: '.', thousands: ' ',}, money: false, }
            ),
        },
        styles: Object,
    },
    data: () => ({
        value_text: '',
        properties_text: {},
        styles_text: {},
        float_model: {},
        rules: {
          required: value => !!value || 'Поле обязательное для ввода',
          not_required: value => true,
        },
        float_model: {},
    }),
    computed: {
        float_model2() {
            let float_model = JSON.parse(JSON.stringify(this.float_model));
            return float_model;
        },
    },
    watch: {
        value_text(val) {
            this.updateInput();
        },
    },
    created () {
        this.value_text = this.value_component;
        this.properties_text = this.properties;
        this.styles_text = this.styles;
        this.float_model = this.properties_text.float_text;
        //console.log(this.properties_text);
    },
    methods: {
        updateInput() {
            if (!this.value_text) {
                return this.$emit('input', '');
            }
            return this.$emit('input', this.value_text);
        },
        onPaste(event){
            console.log(event);
        },
    },
}
</script>