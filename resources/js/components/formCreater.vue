<template>
    <div class="fill-height grey darken-3" v-resize="onResize">
        <component is="style">
            body{
                line-height: 1;
            }
            .scroll-container {
                display: block;
                max-height: {{ heightBlock }}px;
                overflow-y: auto;
                scroll-behavior: smooth;
            }
            .scroll-container1 {
                display: block;
                max-height: {{ heightBlock1 }}px;
                overflow-y: auto;
                scroll-behavior: smooth;
            }
            .scroll-container2 {
                display: block;
                max-height: {{ heightBlock + 20 }}px;
                overflow-y: auto;
                scroll-behavior: smooth;
            }
        </component>
        <v-container class="fill-height pa-7" fluid>
            <v-card elevation="2" class="ml-auto mr-5" outlined min-height="100%" :height="heightBlockDiv" :width="widthBlockDiv ? `${widthBlock1}px` : '60%'" max-height="100%">
                <v-tabs v-model="tab_form" show-arrows background-color="transparent" color="basil" grow active-class="blue-grey lighten-3">
                    <v-tab v-for="item in items_tab_form" :key="item">{{ item }}</v-tab>
                </v-tabs>
                <v-tabs-items v-model="tab_form">
                    <v-tab-item>
                        <div v-show="false">{{double_label}}</div>
                        <div class="ma-5 scroll-container px-5">
                            <div>
                                <v-row class="mx-2 mb-0 mt-2" align="center" justify="end" v-show="itemHeader.isactive">
                                    <span class="w3-tag w3-dark-grey">{{ itemHeader.name }}</span>
                                </v-row>
                                <v-row class="mt-0">
                                    <v-col class="pt-1">
                                        <v-card
                                            :style="itemHeader.isactive ? 'border:4px dashed white' : 'border:2px dashed white'"
                                            :class="itemHeader.isactive ? 'w3-border-dark-grey w3-grey' : ''"
                                            class="w3-border-grey pa-2 mb-2" outlined @click="onChangeHeader()"
                                        >
                                            <v-row>
                                                <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0">
                                                    <component v-bind:is="itemHeader.type" :id_comp="itemHeader.id"
                                                        :properties="itemHeader.properties" :styles="styles[itemHeader.type]"
                                                        :value_component="itemHeader.value">
                                                    </component>
                                                </v-col>
                                            </v-row>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </div>
                            <div>
                            <Container
                                group-name="1" @drop="onDrop('items', $event)" :get-child-payload="getChildPayload2" drag-handle-selector=".column-drag-handle"
                                :drop-placeholder="dropPlaceholderOptions"
                            >
                                <Draggable v-for="(item, key) in items" :key="item.id">
                                    <v-row class="mx-2 mb-0 mt-2" align="center" justify="end" v-show="item.isactive">
                                        <span class="w3-tag w3-indigo">{{ item.name }}</span>
                                    </v-row>
                                    <v-row class="mt-0">
                                        <v-col class="pt-1">
                                            <v-card :class="item.isactive ? 'w3-topbar w3-bottombar w3-leftbar w3-rightbar w3-border-indigo' : ''"
                                            class="w3-border w3-border-light-blue pa-2 mb-2" outlined @click="onChange(item)">
                                                <v-row>
                                                    <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0">
                                                        <component v-bind:is="item.type" :id_comp="item.id"
                                                        :styles="styles[item.type]"
                                                        :properties="item.properties" :value_component="item.value" v-model="item.value"></component>
                                                    </v-col>
                                                    <v-col cols="2" class="flex-grow-0 flex-shrink-0 mb-3 mt-3 mr-3 column-drag-handle w3-border w3-border-light-blue text-center" :class="item.isactive ? 'w3-topbar w3-bottombar w3-leftbar w3-rightbar w3-border-indigo' : ''">
                                                        <v-icon x-large>mdi-drag</v-icon>
                                                    </v-col>
                                                </v-row>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                    <v-row class="mx-2 my-0" align="center" justify="end" v-show="item.isactive">
                                        <v-btn color="indigo" class="mr-2" @click="toUp(item)" :disabled="!key"><v-icon class="white--text">mdi-arrow-up-bold</v-icon></v-btn>
                                        <v-btn color="indigo" class="mr-2" @click="toDown(item)" :disabled="key + 1 == items.length"><v-icon class="white--text">mdi-arrow-down-bold</v-icon></v-btn>
                                        <v-btn color="indigo" class="mr-2" @click="addField(item)"><v-icon class="white--text">mdi-plus</v-icon></v-btn>
                                        <v-btn color="indigo" @click="deleteField(item)"><v-icon class="white--text">mdi-delete-outline</v-icon></v-btn>
                                    </v-row>
                                </Draggable>
                            </Container>
                            </div>
                            <div class="pa-0" style="line-height: normal;">
                                <v-row class="mx-2 mb-0 mt-2" align="center" justify="end" v-show="itemSubmit.isactive">
                                    <span class="w3-tag w3-dark-grey">{{ itemSubmit.name }}</span>
                                </v-row>
                                <v-row class="mt-0 mb-0">
                                    <v-col class="pt-1">
                                        <v-card
                                            :style="itemSubmit.isactive ? 'border:4px dashed white' : 'border:2px dashed white'"
                                            :class="itemSubmit.isactive ? 'w3-border-dark-grey w3-grey' : ''"
                                            class="w3-border-grey pa-2 mb-2" outlined @click="onChangeSubmit()"
                                        >
                                            <v-row>
                                                <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0">
                                                    <component v-bind:is="itemSubmit.type" :id_comp="itemSubmit.id"
                                                        :properties="itemSubmit.properties" :styles="styles[itemSubmit.type]"
                                                        :submitButton="submitButton" :disabled="false"
                                                        :value_component="itemSubmit.value">
                                                    </component>
                                                </v-col>
                                            </v-row>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </div>
                        </div>
                    </v-tab-item>
                    <v-tab-item>
                        <div v-show="false">{{resultArray}}</div>
                        <form-preview v-if="tab_form == 1" :heightBlockDiv="heightBlockDiv" :widthBlockDiv="widthBlockDiv" v-model="resultArray"></form-preview>
                    </v-tab-item>
                </v-tabs-items>
            </v-card>
            <v-card elevation="0" class="mr-auto" :width="widthBlockDiv ? `${widthBlock2}px`: '35%'" v-show="tab_form != 0">
            </v-card>
            <v-card elevation="2" class="mr-auto" outlined min-height="100%" :width="widthBlockDiv ? `${widthBlock2}px`: '35%'" :height="heightBlockDiv" v-show="tab_form == 0">
                    <v-tabs v-model="tab" show-arrows background-color="transparent" color="basil" grow active-class="blue-grey lighten-3">
                <v-tab v-for="item in items_tab" :key="item">{{ item }}</v-tab>
            </v-tabs>
            <v-tabs-items v-model="tab" active-class="blue-grey lighten-5 fill-height">
                <v-tab-item>
                    <div class="scroll-container2 px-5">
                        <v-row class="mt-2 mb-0 mx-5">
                            <v-alert class="mb-0 pa-2" type="info" style="line-height:normal;">Для добавления элемента используйте двойной клик мыши на элементе или перетащите его на форму</v-alert>
                        </v-row>
                        <v-row class="ma-2">
                            <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0 pa-1" height="100%">
                                <div>
                                    <Container behaviour="copy" group-name="1" :get-child-payload="getChildPayload1">
                                        <Draggable class="ma-1" v-for="(element, key) in elements" :key="key">
                                            <div class="draggable-item" @dblclick="add_element(element)">
                                                {{element.name}}
                                            </div>
                                        </Draggable>
                                    </Container>
                                </div>
                            </v-col>
                            <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0 pa-1" height="100%">
                                <div>
                                    <Container behaviour="copy" group-name="1" :get-child-payload="getChildPayload3">
                                        <Draggable class="ma-1" v-for="(element, key) in elements2" :key="key">
                                            <div class="draggable-item" @dblclick="add_element(element)">
                                                {{element.name}}
                                            </div>
                                        </Draggable>
                                    </Container>
                                </div>
                            </v-col>
                        </v-row>
                    </div>
                </v-tab-item>
                <v-tab-item>
                    <v-row class="ma-2">
                        <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0 pa-1" height="100%" v-if="active_header">
                            <label class="w3-text ma-1">Название формы</label>
                            <v-text-field dense placeholder="Введите название формы" outlined requared maxlength="1000" counter v-model="itemHeader.properties.label"></v-text-field>
                        </v-col>
                        <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0 pa-1" height="100%" v-if="active_submit">
                            <label class="w3-text ma-1">Название кнопки</label>
                            <v-text-field dense placeholder="Введите название кнопки" outlined requared maxlength="50" counter v-model="itemSubmit.properties.label"></v-text-field>
                        </v-col>
                        <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0 pa-1" height="100%" v-if="active_index != null">
                            <div class="scroll-container pr-5" v-if="items[active_index].type == 'form_text_field' || items[active_index].type == 'form_textarea' || items[active_index].type == 'form_text_snils_field'">
                                <label class="w3-text ma-1">Надпись</label>
                                <v-text-field dense placeholder="Введите название надписи" outlined requared maxlength="100" :rules="rules_label" counter v-model="items[active_index].properties.label"></v-text-field>
                                <div class="w3-panel w3-deep-orange">
                                    <h3><b>Внимание!</b></h3>
                                    <p class="text-body-2 font-weight-bold">Надпись должна быть уникальной для всех элементов формы</p>
                                </div>
                                <v-switch class="pl-3" v-model="items[active_index].properties.required" label="Поле обязательное для ввода"></v-switch>
                                <v-switch class="pl-3" v-model="items[active_index].properties.placeholder_availability" label="Текст-заполнитель для ввода"></v-switch>
                                <v-text-field dense placeholder="Введите название текста-заполнителя" outlined requared maxlength="150" counter
                                    v-model="items[active_index].properties.placeholder" v-if="items[active_index].properties.placeholder_availability">
                                </v-text-field>
                                <v-switch class="pl-3" v-model="items[active_index].properties.hint_availability" label="Текст подсказки"></v-switch>
                                <v-text-field dense placeholder="Введите название текста подсказки" outlined requared maxlength="150" counter
                                    v-model="items[active_index].properties.hint" v-if="items[active_index].properties.hint_availability">
                                </v-text-field>
                            </div>
                            <div class="scroll-container pr-5" v-if="items[active_index].type == 'form_text_number'">
                                <label class="w3-text ma-1">Надпись</label>
                                <v-text-field dense placeholder="Введите название надписи" outlined requared maxlength="100" :rules="rules_label" counter v-model="items[active_index].properties.label"></v-text-field>
                                <div class="w3-panel w3-deep-orange">
                                    <h3><b>Внимание!</b></h3>
                                    <p class="text-body-2 font-weight-bold">Надпись должна быть уникальной для всех элементов формы</p>
                                </div>
                                <v-switch class="pl-3" v-model="items[active_index].properties.required" label="Поле обязательное для ввода"></v-switch>
                                <v-switch class="pl-3" v-model="items[active_index].properties.placeholder_availability" label="Текст-заполнитель для ввода"></v-switch>
                                <v-text-field dense placeholder="Введите название текста-заполнителя" outlined requared maxlength="150" counter
                                    v-model="items[active_index].properties.placeholder" v-if="items[active_index].properties.placeholder_availability">
                                </v-text-field>
                                <v-switch class="pl-3" v-model="items[active_index].properties.hint_availability" label="Текст подсказки"></v-switch>
                                <v-text-field dense placeholder="Введите название текста подсказки" outlined requared maxlength="150" counter
                                    v-model="items[active_index].properties.hint" v-if="items[active_index].properties.hint_availability">
                                </v-text-field>
                                <v-input-number :dense="true" label="Минимальное возможное число" :readonly="true" :min="0" :max="99" v-model="items[active_index].properties.min" class="px-5"></v-input-number>
                                <v-input-number :dense="true" label="Максимальное возможное число" :readonly="true" :min="100" :max="100000" step="100" v-model="items[active_index].properties.max" class="px-5"></v-input-number>
                                <v-input-number :dense="true" label="Шаг" :readonly="true" :min="1" :max="5" v-model="items[active_index].properties.step" class="px-5"></v-input-number>
                            </div>
                            <div class="scroll-container pr-3" v-if="items[active_index].type == 'form_select'">
                                <label class="w3-text my-1 ml-2">Надпись</label>
                                <v-text-field dense placeholder="Введите название надписи" class="ml-2" outlined requared maxlength="100" :rules="rules_label" counter v-model="items[active_index].properties.label"></v-text-field>
                                <div class="w3-panel w3-deep-orange">
                                    <h3><b>Внимание!</b></h3>
                                    <p class="text-body-2 font-weight-bold">Надпись должна быть уникальной для всех элементов формы</p>
                                </div>
                                <v-switch class="pl-2" v-model="items[active_index].properties.required" label="Поле обязательное для ввода"></v-switch>
                                <v-switch class="pl-2" v-model="items[active_index].properties.hint_availability" label="Текст подсказки"></v-switch>
                                <v-text-field dense placeholder="Введите название текста подсказки" class="ml-4" outlined requared maxlength="150" counter
                                    v-model="items[active_index].properties.hint" v-if="items[active_index].properties.hint_availability">
                                </v-text-field>
                                <v-row><label class="w3-text ml-6 mb-4">Значения</label></v-row>
                                <v-row v-for="(item, key) in items[active_index].properties.items" :key="key" class="mr-2 ml-0 my-0">
                                    <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0 px-2 py-0">
                                        <v-text-field dense :label="`Значение${key + 1}`" outlined requared maxlength="150" counter v-model="items[active_index].properties.items[key]"></v-text-field>
                                    </v-col>
                                    <v-col cols="2" class="flex-grow-0 flex-shrink-0 pl-0 pt-0">
                                        <v-btn style="height:40px;" outlined :disabled="key < 2" @click="del_item(key)"><v-icon>mdi-close-circle</v-icon></v-btn>
                                    </v-col>
                                </v-row>
                                <v-row class="mx-2 my-0">
                                    <v-btn depressed color="primary" @click="add_item()"><v-icon>mdi-plus</v-icon> Добавить значение</v-btn>
                                </v-row>
                            </div>
                            <div class="scroll-container pr-3" v-if="items[active_index].type == 'form_radio_group'">
                                <label class="w3-text my-1 ml-2">Надпись</label>
                                <v-text-field dense placeholder="Введите название надписи" class="ml-2" outlined requared maxlength="100" :rules="rules_label" counter v-model="items[active_index].properties.label"></v-text-field>
                                <div class="w3-panel w3-deep-orange">
                                    <h3><b>Внимание!</b></h3>
                                    <p class="text-body-2 font-weight-bold">Надпись должна быть уникальной для всех элементов формы</p>
                                </div>
                                <v-row><label class="w3-text ml-6 mb-4">Значения</label></v-row>
                                <v-row v-for="(item, key) in items[active_index].properties.items" :key="key" class="mr-2 ml-0 my-0">
                                    <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0 px-2 py-0">
                                        <v-text-field dense :label="`Значение${key + 1}`" outlined requared maxlength="150" counter v-model="items[active_index].properties.items[key]"></v-text-field>
                                    </v-col>
                                    <v-col cols="2" class="flex-grow-0 flex-shrink-0 pl-0 pt-0">
                                        <v-btn style="height:40px;" outlined :disabled="key < 2" @click="del_item(key)"><v-icon>mdi-close-circle</v-icon></v-btn>
                                    </v-col>
                                </v-row>
                                <v-row class="mx-2 my-0">
                                    <v-btn depressed color="primary" @click="add_item()"><v-icon>mdi-plus</v-icon> Добавить значение</v-btn>
                                </v-row>
                            </div>
                            <div class="scroll-container pr-3" v-if="items[active_index].type == 'form_checkbox'">
                                <label class="w3-text my-1 ml-2">Надпись</label>
                                <v-text-field dense placeholder="Введите название надписи" class="ml-2" outlined requared maxlength="100" :rules="rules_label" counter v-model="items[active_index].properties.label"></v-text-field>
                                <div class="w3-panel w3-deep-orange">
                                    <h3><b>Внимание!</b></h3>
                                    <p class="text-body-2 font-weight-bold">Надпись должна быть уникальной для всех элементов формы</p>
                                </div>
                                <v-row><label class="w3-text ml-6 mb-4">Значения</label></v-row>
                                <v-row v-for="(item, key) in items[active_index].properties.items" :key="key" class="mr-2 ml-0 my-0">
                                    <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0 px-2 py-0">
                                        <v-text-field dense :label="`Значение${key + 1}`" outlined requared maxlength="150" counter v-model="items[active_index].properties.items[key]"></v-text-field>
                                    </v-col>
                                    <v-col cols="2" class="flex-grow-0 flex-shrink-0 pl-0 pt-0">
                                        <v-btn style="height:40px;" outlined :disabled="key < 1" @click="del_item(key)"><v-icon>mdi-close-circle</v-icon></v-btn>
                                    </v-col>
                                </v-row>
                                <v-row class="mx-2 my-0">
                                    <v-btn depressed color="primary" @click="add_item()"><v-icon>mdi-plus</v-icon> Добавить значение</v-btn>
                                </v-row>
                            </div>
                            <div class="scroll-container pr-3" v-if="items[active_index].type == 'form_rating'">
                                <label class="w3-text my-1 ml-2">Надпись</label>
                                <v-text-field dense placeholder="Введите название надписи" class="ml-2" outlined requared maxlength="100" :rules="rules_label" counter v-model="items[active_index].properties.label"></v-text-field>
                                <div class="w3-panel w3-deep-orange">
                                    <h3><b>Внимание!</b></h3>
                                    <p class="text-body-2 font-weight-bold">Надпись должна быть уникальной для всех элементов формы</p>
                                </div>
                                <v-input-number :dense="true" label="Количество отображаемых иконок" :readonly="true" :min="5" :max="10" v-model="items[active_index].properties.length" class="px-5"></v-input-number>
                                <v-switch class="pl-3" v-model="items[active_index].properties.hint_availability" label="Текст подсказки"></v-switch>
                                <v-text-field dense placeholder="Введите название текста подсказки" outlined requared maxlength="150" counter
                                    v-model="items[active_index].properties.hint" v-if="items[active_index].properties.hint_availability">
                                </v-text-field>
                            </div>
                            <div class="scroll-container pr-3" v-if="items[active_index].type == 'form_date_field'">
                                <label class="w3-text ma-1">Надпись</label>
                                <v-text-field dense placeholder="Введите название надписи" outlined requared maxlength="100" :rules="rules_label" counter v-model="items[active_index].properties.label"></v-text-field>
                                <div class="w3-panel w3-deep-orange">
                                    <h3><b>Внимание!</b></h3>
                                    <p class="text-body-2 font-weight-bold">Надпись должна быть уникальной для всех элементов формы</p>
                                </div>
                                <v-switch class="pl-3" v-model="items[active_index].properties.required" label="Поле обязательное для ввода"></v-switch>
                                <v-switch class="pl-3" v-model="items[active_index].properties.max_date_now" label="Дата не больше даты на момент заполнения"></v-switch>
                            </div>
                            <div class="scroll-container pr-3" v-if="items[active_index].type == 'form_time_field'">
                                <label class="w3-text ma-1">Надпись</label>
                                <v-text-field dense placeholder="Введите название надписи" outlined requared maxlength="100" :rules="rules_label" counter v-model="items[active_index].properties.label"></v-text-field>
                                <div class="w3-panel w3-deep-orange">
                                    <h3><b>Внимание!</b></h3>
                                    <p class="text-body-2 font-weight-bold">Надпись должна быть уникальной для всех элементов формы</p>
                                </div>
                                <v-switch class="pl-3" v-model="items[active_index].properties.required" label="Поле обязательное для ввода"></v-switch>
                                <v-switch class="pl-3" v-model="items[active_index].properties.hint_availability" label="Текст подсказки"></v-switch>
                                <v-text-field dense placeholder="Введите название текста подсказки" outlined requared maxlength="150" counter
                                    v-model="items[active_index].properties.hint" v-if="items[active_index].properties.hint_availability">
                                </v-text-field>
                            </div>
                            <div class="scroll-container pr-5" v-if="items[active_index].type == 'form_text_float'">
                                <label class="w3-text ma-1">Надпись</label>
                                <v-text-field dense placeholder="Введите название надписи" outlined requared maxlength="100" :rules="rules_label" counter v-model="items[active_index].properties.label"></v-text-field>
                                <div class="w3-panel w3-deep-orange">
                                    <h3><b>Внимание!</b></h3>
                                    <p class="text-body-2 font-weight-bold">Надпись должна быть уникальной для всех элементов формы</p>
                                </div>
                                <v-switch class="pl-3" v-model="items[active_index].properties.required" label="Поле обязательное для ввода"></v-switch>
                                <v-switch class="pl-3" v-model="items[active_index].properties.placeholder_availability" label="Текст-заполнитель для ввода"></v-switch>
                                <v-text-field dense placeholder="Введите название текста-заполнителя" outlined requared maxlength="150" counter
                                    v-model="items[active_index].properties.placeholder" v-if="items[active_index].properties.placeholder_availability">
                                </v-text-field>
                                <v-switch class="pl-3" v-model="items[active_index].properties.hint_availability" label="Текст подсказки"></v-switch>
                                <v-text-field dense placeholder="Введите название текста подсказки" outlined requared maxlength="150" counter
                                    v-model="items[active_index].properties.hint" v-if="items[active_index].properties.hint_availability">
                                </v-text-field>
                                <v-switch class="pl-3" v-model="items[active_index].properties.money" label="Денежный формат"></v-switch>
                            </div>
                        </v-col>
                        <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0 pa-1" height="100%" v-if="((!active_header) && (!active_submit) && (active_index == null))">
                        <v-alert type="warning">Не выбран элемент</v-alert>
                        </v-col>
                    </v-row>
                </v-tab-item>
                <v-tab-item>
                    <v-row class="ma-2">
                        <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0 pa-1" height="100%">
                            <div class="scroll-container pr-5">
                                <v-expansion-panels accordion>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Название формы</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <div class="scroll-container pr-5">
                                                <v-row class="ma-2">
                                                    <v-switch v-model="styles.form_header.bold" label="Жирность текста увеличена" class="ma-0"></v-switch>
                                                </v-row>
                                                <label class="w3-text ma-1">Цвет текста</label>
                                                <v-row class="ma-2">
                                                    <v-color-picker class="ma-2" show-swatches :swatches="swatches" v-model="styles.form_header.color" width="240"></v-color-picker>
                                                </v-row>
                                            </div>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Кнопка формы</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <div class="scroll-container pr-5">
                                                <v-row class="ma-2">
                                                    <v-switch v-model="styles.form_button_submit.rounded" label="Закругленные границы" class="ma-0"></v-switch>
                                                    <v-switch v-model="styles.form_button_submit.dark" label="Белый цвет кнопки" class="ma-0" v-if="!styles.form_button_submit.outlined"></v-switch>
                                                    <v-switch v-model="styles.form_button_submit.outlined" label="Кнопки обведены границей" class="ma-0"></v-switch>
                                                </v-row>
                                                <label class="w3-text ma-1">Цвет кнопки</label>
                                                <v-row class="ma-2">
                                                    <v-color-picker class="ma-2" show-swatches :swatches="swatches" v-model="styles.form_button_submit.color" width="240"></v-color-picker>
                                                </v-row>
                                            </div>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Текстовое поле</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <v-row class="ma-2">
                                                <v-switch v-model="styles.form_text_field.dense" label="Высота поля уменьшена" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_text_field.clearable" label="Кнопка очистки поля" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_text_field.rounded" label="Закругленные границы" class="ma-0"></v-switch>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                            <v-row class="ma-2">
                                                <v-radio-group v-model="styles.form_text_field.type_style">
                                                    <v-row>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                            <v-radio label="С границей" value="outlined"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-text-field value="Пример текста" dense outlined label="С границей" readonly></v-text-field>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                        <v-radio label="С фоном" value="filled"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-text-field value="Пример текста" dense filled label="С фоном" readonly></v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                </v-radio-group>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Текстовая область</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <v-row class="ma-2">
                                                <v-switch v-model="styles.form_textarea.dense" label="Высота поля уменьшена" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_textarea.clearable" label="Кнопка очистки поля" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_textarea.rounded" label="Закругленные границы" class="ma-0"></v-switch>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                            <v-row class="ma-2">
                                                <v-radio-group v-model="styles.form_textarea.type_style">
                                                    <v-row>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                            <v-radio label="С границей" value="outlined"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-textarea value="Пример текста" dense outlined label="С границей" rows="1" readonly></v-textarea>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                        <v-radio label="С фоном" value="filled"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-textarea value="Пример текста" dense filled label="С фоном" rows="1" readonly></v-textarea>
                                                        </v-col>
                                                    </v-row>
                                                </v-radio-group>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Числовое поле</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <v-row class="ma-2">
                                                <v-switch v-model="styles.form_text_number.dense" label="Высота поля уменьшена" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_text_number.clearable" label="Кнопка очистки поля" class="ma-0"></v-switch>
                                            </v-row>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Список</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <v-row class="ma-2">
                                                <v-switch v-model="styles.form_select.dense" label="Высота поля уменьшена" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_select.clearable" label="Кнопка очистки поля" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_select.rounded" label="Закругленные границы" class="ma-0"></v-switch>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                            <v-row class="ma-2">
                                                <v-radio-group v-model="styles.form_select.type_style">
                                                    <v-row>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                            <v-radio label="С границей" value="outlined"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-select value="Пример текста" dense outlined label="С границей" readonly></v-select>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                        <v-radio label="С фоном" value="filled"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-select value="Пример текста" dense filled label="С фоном" readonly></v-select>
                                                        </v-col>
                                                    </v-row>
                                                </v-radio-group>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Радиокнопки</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <div class="scroll-container pr-5">
                                                <v-row class="ma-2">
                                                    <v-switch v-model="styles.form_radio_group.dense" label="Высота радиокнопки уменьшена" class="ma-0"></v-switch>
                                                </v-row>
                                                <v-divider class="ma-0"></v-divider>
                                                <v-row class="ma-2">
                                                    <v-radio-group v-model="styles.form_radio_group.position">
                                                        <v-radio label="Радиокнопки вертикально" value="column"></v-radio>
                                                        <v-radio label="Радиокнопки горизонтально" value="row"></v-radio>
                                                    </v-radio-group>
                                                </v-row>
                                                <v-divider class="ma-0"></v-divider>
                                                <label class="w3-text ma-1">Цвет кнопки</label>
                                                <v-row class="ma-2">
                                                    <v-color-picker class="ma-2" show-swatches :swatches="swatches" v-model="styles.form_radio_group.color" width="240"></v-color-picker>
                                                </v-row>
                                            </div>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Флажки</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <div class="scroll-container pr-5">
                                                <v-row class="ma-2">
                                                    <v-switch v-model="styles.form_checkbox.dense" label="Высота флажка уменьшена" class="ma-0"></v-switch>
                                                </v-row>
                                                <v-divider class="ma-0"></v-divider>
                                                <v-row class="ma-2">
                                                    <v-radio-group v-model="styles.form_checkbox.position">
                                                        <v-radio label="Флажки вертикально" value="column"></v-radio>
                                                        <v-radio label="Флажки горизонтально" value="row"></v-radio>
                                                    </v-radio-group>
                                                </v-row>
                                                <v-divider class="ma-0"></v-divider>
                                                <label class="w3-text ma-1">Цвет флажка</label>
                                                <v-row class="ma-2">
                                                    <v-color-picker class="ma-2" show-swatches :swatches="swatches" v-model="styles.form_checkbox.color" width="240"></v-color-picker>
                                                </v-row>
                                            </div>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Поле ввода даты</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <v-row class="ma-2">
                                                <v-switch v-model="styles.form_date_field.dense" label="Высота поля уменьшена" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_date_field.clearable" label="Кнопка очистки поля" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_date_field.rounded" label="Закругленные границы" class="ma-0"></v-switch>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                            <v-row class="ma-2">
                                                <v-radio-group v-model="styles.form_date_field.type_style">
                                                    <v-row>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                            <v-radio label="С границей" value="outlined"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-text-field value="Пример текста" dense outlined label="С границей" readonly></v-text-field>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                        <v-radio label="С фоном" value="filled"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-text-field value="Пример текста" dense filled label="С фоном" readonly></v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                </v-radio-group>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Поле ввода времени</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <v-row class="ma-2">
                                                <v-switch v-model="styles.form_time_field.dense" label="Высота поля уменьшена" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_time_field.clearable" label="Кнопка очистки поля" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_time_field.rounded" label="Закругленные границы" class="ma-0"></v-switch>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                            <v-row class="ma-2">
                                                <v-radio-group v-model="styles.form_time_field.type_style">
                                                    <v-row>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                            <v-radio label="С границей" value="outlined"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-text-field value="Пример текста" dense outlined label="С границей" readonly></v-text-field>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                        <v-radio label="С фоном" value="filled"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-text-field value="Пример текста" dense filled label="С фоном" readonly></v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                </v-radio-group>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Поле ввода СНИЛС</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <v-row class="ma-2">
                                                <v-switch v-model="styles.form_text_snils_field.dense" label="Высота поля уменьшена" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_text_snils_field.clearable" label="Кнопка очистки поля" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_text_snils_field.rounded" label="Закругленные границы" class="ma-0"></v-switch>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                            <v-row class="ma-2">
                                                <v-radio-group v-model="styles.form_text_snils_field.type_style">
                                                    <v-row>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                            <v-radio label="С границей" value="outlined"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-text-field value="Пример текста" dense outlined label="С границей" readonly></v-text-field>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                        <v-radio label="С фоном" value="filled"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-text-field value="Пример текста" dense filled label="С фоном" readonly></v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                </v-radio-group>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Рейтинг</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <div class="scroll-container pr-5">
                                                <v-row class="ma-2">
                                                    <v-switch v-model="styles.form_rating.dense" label="Расстояние в компонента уменьшено" class="ma-0"></v-switch>
                                                </v-row>
                                                <v-divider class="ma-0"></v-divider>
                                                <v-row class="ma-2">
                                                    <v-input-number :dense="true" label="Размер иконок" :readonly="true" :min="25" :max="50" v-model="styles.form_rating.size" class="px-5"></v-input-number>
                                                    <v-radio-group v-model="styles.form_rating.icon" row>
                                                        <v-radio v-for="(icon, key) in icon_list" :key="key" :value="icon">
                                                            <template v-slot:label>
                                                                <v-icon :color="styles.form_rating.color">{{icon}}</v-icon>
                                                            </template>
                                                        </v-radio>
                                                    </v-radio-group>
                                                </v-row>
                                                <v-divider class="ma-0"></v-divider>
                                                <label class="w3-text ma-1">Цвет флажка</label>
                                                <v-row class="ma-2">
                                                    <v-color-picker class="ma-2" show-swatches :swatches="swatches" v-model="styles.form_rating.color" width="240"></v-color-picker>
                                                </v-row>
                                            </div>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Десятичное числовое поле</v-expansion-panel-header>
                                        <v-expansion-panel-content class="pa-0">
                                            <v-row class="ma-2">
                                                <v-switch v-model="styles.form_text_float.dense" label="Высота поля уменьшена" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_text_float.clearable" label="Кнопка очистки поля" class="ma-0"></v-switch>
                                                <v-switch v-model="styles.form_text_float.rounded" label="Закругленные границы" class="ma-0"></v-switch>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                            <v-row class="ma-2">
                                                <v-radio-group v-model="styles.form_text_float.type_style">
                                                    <v-row>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                            <v-radio label="С границей" value="outlined"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-text-field value="Пример текста" dense outlined label="С границей" readonly></v-text-field>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="px-2">
                                                        <v-radio label="С фоном" value="filled"></v-radio>
                                                        </v-col>
                                                        <v-col cols="12" sm="6" md="6" class="py-0 px-2">
                                                            <v-text-field value="Пример текста" dense filled label="С фоном" readonly></v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                </v-radio-group>
                                            </v-row>
                                            <v-divider class="ma-0"></v-divider>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </div>
                        </v-col>
                    </v-row>
                </v-tab-item>
            </v-tabs-items>
            </v-card>
        </v-container>
    </div>
</template>

<style>

.form-line {
    cursor: move;
}
</style>

<script>
import { Container, Draggable } from "vue-smooth-dnd";
import { applyDrag, generateItems } from "./utils/helpers";
import form_text_field from './form_text_field.vue';
import form_textarea from './form_textarea.vue';
import form_text_number from './form_text_number.vue';
import form_select from './form_select.vue';
import form_radio_group from './form_radio_group.vue';
import form_checkbox from './form_checkbox.vue';
import form_rating from './form_rating.vue';
import form_date_field from './form_date_field.vue';
import form_time_field from './form_time_field.vue';
import form_text_snils_field from './form_text_snils_field.vue';
import form_header from './form_header.vue';
import form_button_submit from './form_button_submit.vue';
import formPreview from './formPreview.vue';
import form_text_float from './form_text_float.vue';

export default {
    components: {
        Container,
        Draggable,
        form_text_field,
        form_textarea,
        form_text_number,
        form_select,
        form_radio_group,
        form_checkbox,
        form_rating,
        form_date_field,
        form_time_field,
        form_text_snils_field,
        form_header,
        form_button_submit,
        formPreview,
        form_text_float,
    },
    props: {
        mainpath: String,
        value: Object,
        heightBlockDiv: {
			type: Number,
			default: null,
		},
        widthBlockDiv: {
			type: Number,
			default: null,
		},
        items_form: {
			type: Array,
			default: () => (
                []
            ),
        },
        itemHeader: {
            type: Object,
            default: () => (
                {id: null, name: 'Название формы', type: 'form_header', properties: {label: 'Название формы', }, value: null, isactive: false,}
            ),
        },
        itemSubmit: {
            type: Object,
            default: () => (
                {id: null, name: 'Кнопка формы', type: 'form_button_submit', properties: {label: 'Сохранить', }, value: null, isactive: false,}
            ),
        },
        styles: {
            type: Object,
            default: () => (
                {
                    form_text_field: {
                        dense: true, clearable: true, type_style: 'outlined', rounded: false,
                    },
                    form_textarea: {
                        dense: true, clearable: true, type_style: 'outlined', rounded: false,
                    },
                    form_text_number: {
                        dense: true, clearable: true,
                    },
                    form_select: {
                        dense: true, clearable: true, type_style: 'outlined', rounded: false,
                    },
                    form_radio_group: {
                        dense: false, position: 'column', color: '#3F51B5',
                    },
                    form_checkbox: {
                        dense: false, position: 'column', color: '#3F51B5',
                    },
                    form_rating: {
                        dense: false, icon: 'mdi-heart', color: '#3F51B5', size: 25,
                    },
                    form_date_field: {
                        dense: true, clearable: true, type_style: 'outlined', rounded: false,
                    },
                    form_time_field: {
                        dense: true, clearable: true, type_style: 'outlined', rounded: false,
                    },
                    form_text_snils_field: {
                        dense: true, clearable: true, type_style: 'outlined', rounded: false,
                    },
                    form_header: {
                        color: '#3F51B5', bold: true,
                    },
                    form_button_submit: {
                        color: '#167719', rounded: false, dark: true, outlined: false,
                    },
                    form_text_float: {
                        dense: true, clearable: true, type_style: 'outlined', rounded: false,
                    },
                }
            ),
        },
    },
    data: () => ({
        form_scheme: {},
        //itemHeader: {id: null, name: 'Название формы', type: 'form_header', properties: {label: 'Название формы', }, value: null, isactive: false,},
        //itemSubmit: {id: null, name: 'Кнопка формы', type: 'form_button_submit', properties: {label: 'Сохранить', }, value: null, isactive: false,},
        items: [],
        tab: 0,
        tab_form: 0,
        test:0,
        /*styles: {
            form_text_field: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_textarea: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_text_number: {
                dense: true, clearable: true,
            },
            form_select: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_radio_group: {
                dense: false, position: 'column', color: '#3F51B5',
            },
            form_checkbox: {
                dense: false, position: 'column', color: '#3F51B5',
            },
            form_rating: {
                dense: false, icon: 'mdi-heart', color: '#3F51B5', size: 25,
            },
            form_date_field: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_time_field: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_text_snils_field: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_header: {
                color: '#3F51B5', bold: true,
            },
            form_button_submit: {
                color: '#167719', rounded: false, dark: true, outlined: false,
            },
            form_text_float: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
        },*/
        properties: {},
        elements: [
            {id: null, name: 'Текстовое поле', type: 'form_text_field', properties: {label: 'Надпись', placeholder_availability: false, placeholder: 'Текст-заполнитель для ввода', hint_availability: false, hint: 'Текст подсказки', required: true,}, value: '', isactive: false,},
            {id: null, name: 'Числовое поле', type: 'form_text_number', properties: {label: 'Надпись', placeholder_availability: false, placeholder: 'Текст-заполнитель для ввода', hint_availability: false, hint: 'Текст подсказки', required: true, min: '1', max: '1000', step: '1',}, value: '1', isactive: false,},
            {id: null, name: 'Радиокнопки', type: 'form_radio_group', properties: {label: 'Надпись', hint_availability: false, hint: 'Текст подсказки', items: ['Значение1', 'Значение2'],}, value: null, isactive: false,},
            {id: null, name: 'Поле ввода даты', type: 'form_date_field', properties: {label: 'Надпись', required: true, max_date_now: true,}, value: '', isactive: false,},
            {id: null, name: 'Поле ввода СНИЛС', type: 'form_text_snils_field', properties: {label: 'СНИЛС', placeholder_availability: false, placeholder: 'Текст-заполнитель для ввода', hint_availability: false, hint: 'Текст подсказки', required: true,}, value: '', isactive: false,},
            {id: null, name: 'Десятичное числовое поле', type: 'form_text_float', properties: {label: 'Надпись', placeholder_availability: false, placeholder: 'Текст-заполнитель для ввода', hint_availability: false, hint: 'Текст подсказки', required: true, float_text: { decimal: '.', thousands: ' ',}, money: false, }, value: '', isactive: false,},
        ],
        elements2: [
            {id: null, name: 'Текстовая область', type: 'form_textarea', properties: {label: 'Надпись', placeholder_availability: false, placeholder: 'Текст-заполнитель для ввода', hint_availability: false, hint: 'Текст подсказки', required: true,}, value: '', isactive: false,},
            {id: null, name: 'Список', type: 'form_select', properties: {label: 'Надпись', placeholder_availability: false, placeholder: 'Текст-заполнитель для ввода', hint_availability: false, hint: 'Текст подсказки', required: true, items: ['Значение1', 'Значение2'],}, value: null, isactive: false,},
            {id: null, name: 'Флажки', type: 'form_checkbox', properties: {label: 'Надпись', hint_availability: false, hint: 'Текст подсказки', items: ['Значение1', 'Значение2'],}, value: [], isactive: false,},
            {id: null, name: 'Поле ввода времени', type: 'form_time_field', properties: {label: 'Надпись', required: true, hint_availability: false, hint: 'Текст подсказки', max_time_now: true,}, value: '', isactive: false,},
            {id: null, name: 'Рейтинг', type: 'form_rating', properties: {label: 'Надпись', hint_availability: false, hint: 'Текст подсказки', length: 5,}, value: 1, isactive: false,},
        ],
        items_tab: ['Элементы', 'Свойства', 'Стили',],
        items_tab_form: ['Построение формы', 'Предварительный просмотр',],
        active_index: null,
        active_field: false,
        active_header: false,
        active_submit: false,
        windowSize: { x: 0, y: 0, },
        heightBlock: 0,
        heightBlock1: 0,
        widthBlock1: 0,
        widthBlock2: 0,
        counter: 0,
        dropPlaceholderOptions: {
            className: 'drop-preview',
            animationDuration: '150',
            showOnTop: true
        },
        swatches: [
            ['#F44336', '#E91E63', '#9C27B0'],
            ['#673AB7', '#3F51B5', '#2196F3'],
            ['#03A9F4', '#00BCD4', '#009688'],
            ['#4CAF50', '#8BC34A', '#CDDC39'],
            ['#FFC107', '#FF9800', '#FF5722'],
            ['#795548', '#607D8B', '#757575'],
        ],
        icon_list: ['mdi-heart', 'mdi-star', 'mdi-decagram', 'mdi-lightbulb', 'mdi-thumb-up', 'mdi-gift', 'mdi-circle'],
    }),
    computed: {
        double_label() {
            let items = JSON.parse(JSON.stringify(this.items));
            if (items.length > 0) {
                for (let index = 0; index < items.length; index++) {
                    const element = items[index];
                    let double = items.filter((item) => item.properties.label == element.properties.label);
                    if (double.length > 1) return true;
                }
                return false;
            }
            else {
                return false;
            }
        },
        rules_label() {
            return [
                (v) => {
                    if (!v || v.length < 1) {
                        return 'Надпись не введена';
                    }
                    else if (v.length > 0) {
                        let items = JSON.parse(JSON.stringify(this.items));
                        let double = items.filter((item) => item.properties.label == v);
                        //console.log(double);
                        if (double.length > 1) return 'Введен дубликат надписи';
                        return true;
                    }
                }
            ]
        },
        resultArray: {
            get: function () {
                let itemHeader = JSON.parse(JSON.stringify(this.itemHeader));
                let items = JSON.parse(JSON.stringify(this.items));
                let itemSubmit = JSON.parse(JSON.stringify(this.itemSubmit));
                let styles = JSON.parse(JSON.stringify(this.styles));
                itemHeader.id = -1;
                itemSubmit.id = -2;
                this.form_scheme.header = itemHeader;
                this.form_scheme.items = items;
                this.form_scheme.submit = itemSubmit;
                this.form_scheme.styles = styles;
                this.$emit('input', this.form_scheme);
                return this.form_scheme;
            },
            set: function (val) {
                //this.form_scheme = JSON.parse(JSON.stringify(val));
            }
        },
    },
    watch: {
        resultArray: {
            handler(newVal, oldVal) {
                this.$emit('input', JSON.parse(JSON.stringify(newVal)));
                //console.log(newVal);
                // do something with the object
            },
            deep: true,
        },
    },
    created () {
        this.init();
    },
    methods: {
        init() {
            this.form_scheme = this.value;
            this.items = this.items_form;
            if (!this.form_scheme) {
                this.form_scheme = {};
                let itemHeader = JSON.parse(JSON.stringify(this.itemHeader));
                let items = JSON.parse(JSON.stringify(this.items));
                let itemSubmit = JSON.parse(JSON.stringify(this.itemSubmit));
                let styles = JSON.parse(JSON.stringify(this.styles));
                itemHeader.id = -1;
                itemSubmit.id = -2;
                this.form_scheme.header = itemHeader;
                this.form_scheme.items = items;
                this.form_scheme.submit = itemSubmit;
                this.form_scheme.styles = styles;
            }
            else {
                if (!this.form_scheme.hasOwnProperty('header')) {
                    let itemHeader = JSON.parse(JSON.stringify(this.itemHeader));
                    itemHeader.id = -1;
                    this.form_scheme.header = itemHeader;
                }
                if (!this.form_scheme.hasOwnProperty('items')) {
                    let items = JSON.parse(JSON.stringify(this.items));
                    this.form_scheme.items = items;
                }
                if (!this.form_scheme.hasOwnProperty('submit')) {
                    let itemSubmit = JSON.parse(JSON.stringify(this.itemSubmit));
                    itemSubmit.id = -2;
                    this.form_scheme.submit = itemSubmit;
                }
                if (!this.form_scheme.hasOwnProperty('styles')) {
                    let styles = JSON.parse(JSON.stringify(this.styles));
                    this.form_scheme.styles = styles;
                }
            }
            this.onResize();
        },
        onDrop (collection, dropResult) {
            //console.log(dropResult.addedIndex);
            if (dropResult.payload) {
                let dropItem = JSON.parse(JSON.stringify(dropResult.payload));
                if (dropItem.id == null) {
                    let new_id = this.max_id() + 1;
                    dropItem.id = new_id;
                    dropItem.properties.label += new_id;
                }
                dropResult.payload = JSON.parse(JSON.stringify(dropItem));
            }
            //this.clear_active();
            this[collection] = applyDrag(this[collection], dropResult);
            if (this.active_index != null) {
                this.clear_active();
                this.active_index = dropResult.addedIndex;
                this.items[this.active_index ].isactive = true;
                this.tab = 1;
            }
        },
        getChildPayload1 (index) {
            //console.log(index);
            return this.elements[index];
        },
        getChildPayload2 (index) {
            //console.log(index);
            return this.items[index];
        },
        getChildPayload3 (index) {
            //console.log(index);
            return this.elements2[index];
        },
        clear_active() {
            this.items.forEach((item) => {item.isactive = false});
            this.itemHeader.isactive = false;
            this.itemSubmit.isactive = false;
            this.active_header = false;
            this.active_submit = false;
            this.active_index = null;
            this.active_field = false;
        },
        onChange(item) {
            //console.log(item);
            this.clear_active();
            let index = this.items.findIndex(el => el.id === item.id);
            this.active_index = index;
            this.items[index].isactive = true;
            this.tab = 1;
            //console.log(index);
        },
        onChangeHeader() {
            this.clear_active();
            this.active_header = true;
            this.itemHeader.isactive = true;
            this.tab = 1;
        },
        onChangeSubmit() {
            this.clear_active();
            this.active_submit = true;
            this.itemSubmit.isactive = true;
            this.tab = 1;
        },
        change_value (id, value) {
            //console.log(id);
            //console.log(value);
            let index = this.items.findIndex(el => el.id === id);
            this.items[index].value = value;
        },
        max_id() {
            if (this.items.length > 0) return Math.max.apply(Math, this.items.map(function(item) { return item.id; }));
            return 0;
        },
        addField(item) {
            let index = this.items.findIndex(el => el.id === item.id);
            let new_field = JSON.parse(JSON.stringify(item));
            let new_id = this.max_id() + 1;
            new_field.id = new_id;
            new_field.properties.label = new_field.properties.label.replace(/\d/g,"");
            new_field.properties.label += new_id;
            this.items.splice(index + 1, 0, new_field);
            this.clear_active();
            this.active_index = index + 1;
            this.items[index + 1].isactive = true;
            this.tab = 1;
        },
        deleteField(item) {
            let index = this.items.findIndex(el => el.id === item.id);
            this.items.splice(index, 1);
            this.tab = 0;
            this.active_index = null;
            this.active_field = false;
        },
        toUp(item) {
            let index = this.items.findIndex(el => el.id === item.id);
            [this.items[index-1], this.items[index]] = [this.items[index], this.items[index-1]];
            this.clear_active();
            this.active_index = index - 1;
            this.items[index - 1].isactive = true;
            this.tab = 1;
        },
        toDown(item) {
            let index = this.items.findIndex(el => el.id === item.id);
            [this.items[index], this.items[index+1]] = [this.items[index+1], this.items[index]];
            this.clear_active();
            this.active_index = index + 1;
            this.items[index + 1].isactive = true;
            this.tab = 1;
        },
        onResize() {
            this.windowSize = { x: window.innerWidth, y: window.innerHeight };
            this.heightBlock = this.windowSize.y - 250;
            this.heightBlock1 = this.windowSize.y - 170;
            if (this.heightBlockDiv) {
                this.heightBlock = this.heightBlockDiv - 100;
                this.heightBlock1 = this.heightBlockDiv - 20;
            }
            if (this.widthBlockDiv) {
                this.widthBlock1 = this.widthBlockDiv * 0.9;
                this.widthBlock2 = this.widthBlock1 * 0.65;
            }
        },
        add_item() {
            this.items[this.active_index].properties.items.push(`Значение${this.items[this.active_index].properties.items.length + 1}`);
        },
        del_item(index) {
            this.items[this.active_index].properties.items.splice(index, 1);
            //console.log(index);
        },
        add_element(element){
            let new_field = JSON.parse(JSON.stringify(element));
            let new_id = this.max_id() + 1;
            new_field.id = new_id;
            new_field.properties.label += new_id;
            this.items.push(new_field);
        },
        submitButton() {
            console.log('submit');
        },
    },
}
</script>