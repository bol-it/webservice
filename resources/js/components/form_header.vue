<template>
    <div class="text-h6" :class="styles_text.bold ? 'font-weight-bold' : ''" :style="`color:${styles_text.color};`">
        {{properties_text.label}}
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

    },
    created () {
        this.value_text = this.value_component;
        this.properties_text = this.properties;
        this.styles_text = this.styles;
        //console.log(this.properties_text);
    },
    methods: {

    },
}
</script>