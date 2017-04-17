<template>
    <div class="search-widget well well-lg">
        <div class="row">
            <div class="col-xs-12 text-center">
                <slot name="header"></slot>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 text-center">
                <span class="error-message">{{ cityMessage }}</span>
            </div>
            <div class="col-xs-6 text-center">
                <span v-if="stateValidationError" class="error-message">{{ stateMessage }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 text-right">
                <div class="form-group">
                    <input class="form-control" type="text" name="city" v-model="city" placeholder="city" :class="{error: cityValidationError}">
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <select class="form-control" name="state" v-model="state" :class="{error: stateValidationError}">
                        <option v-for="(state, index) in stateList" :value="(index)">{{ state }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-center">
                <button type="button" class="btn btn-default" @click="search">Search</button>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
    @import '../../sass/common.scss';
    .search-widget {
        padding: 2em 0;
    }

    .error {
        border: 1px solid #f00;
    }

    .error-message {
        color: #f00;
    }
</style>

<script>
    export default {
        props: [],
        mounted() {
            let searchTerms = [], query;
            let queries = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for(let i=0; i<queries.length; i++) {
                query = queries[i].split('=');
                if (query[0] != 'page') {
                    searchTerms[query[0]] = query[1];
                }
            }
            if ("city" in searchTerms && "state" in searchTerms) {
                this.city = searchTerms["city"];
                this.state = searchTerms["state"];
            } else {
                this.state = 0; //Preselect "State" from select
            }
        },
        methods: {
            search() {
                this.cityValidationError = false;
                this.stateValidationError = false;
                this.cityMessage = "";
                this.stateMessage = "";

                //TODO - Refactor into validate method
                if ("" == this.city) {
                    this.cityValidationError = true;
                    this.cityMessage = "Please enter a city.";
                    return false;
                }
                if (0 == this.state) {
                    this.stateValidationError = true;
                    this.stateMessage = "Please select a state.";
                    return false;
                }

                window.bookMe.Event.fire('city-state-search', {city: this.city, state: this.state})
            }
        },
        data() {
            return {
                city: null,
                state: null,
                type: null,
                stateList: window.states,

                //TODO - Refactor validation handling
                cityValidationError: false,
                cityMessage: "",
                stateValidationError: false,
                stateMessage: ""
            }
        }
    }
</script>