<template>
    <div>
        <v-select
            :label="properties_text.label"
            :hint="properties_text.hint_availability ? properties_text.hint : ''"
            :outlined="styles_text.type_style == 'outlined'"
            :filled="styles_text.type_style == 'filled'"
            :dense="styles_text.dense"
            :clearable="styles_text.clearable"
            :rounded="styles_text.rounded"
            :rules="properties_text.required ? [rules.required] : [rules.not_required]"
            :items="properties_text.items"
            v-model="value_text"
        ></v-select>
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
    },
    data: () => ({
        value_text: '',
        properties_text: {},
        styles_text: {},
        rules: {
          required: value => !!value || 'Поле обязательное для ввода',
          not_required: value => true,
        },
    }),
    computed: {

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
        //console.log(this.properties_text);
    },
    methods: {
        updateInput() {
            if (!this.value_text) {
                return this.$emit('input', '');
            }
            return this.$emit('input', this.value_text);
        },
    },
}
</script>