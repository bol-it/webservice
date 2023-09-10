<template>
    <div>
        <v-radio-group v-model="value_radioGroup" mandatory :column="styles_radio.position == 'column'" :row="styles_radio.position == 'row'" :dense="styles_radio.dense"
            :label="properties_radio.label"
        >
            <v-radio v-for="(item, key) in properties_radio.items" :key="key" :value="item" :label="item" :color="styles_radio.color">
            </v-radio>
        </v-radio-group>
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
        value_radioGroup: '',
        properties_radio: {},
        styles_radio: {},
        rules_radio: [],
        rules: {
          required: value => !!value || 'Поле обязательное для ввода',
          not_required: value => true,
        },
    }),
    computed: {

    },
    watch: {
        value_radioGroup(val) {
            this.updateInput();
        },
    },
    created () {
        this.value_radioGroup = this.value_component;
        this.properties_radio = this.properties;
        if (this.properties_radio.required) {
            this.rules_radio = this.rules_required;
        }
        else {
            this.rules_radio = true;
        }
        this.styles_radio = this.styles;
        //console.log(this.properties_text);
    },
    methods: {
        updateInput() {
            if (!this.value_radioGroup) {
                return this.$emit('input', '');
            }
            return this.$emit('input', this.value_radioGroup);
        },
    },
}
</script>