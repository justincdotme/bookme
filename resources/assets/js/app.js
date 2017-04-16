require('./bootstrap');

//Classes
import Event from './Event';
import State from './State';

//Components
import PropertyPreview from './components/property-preview.vue';

//Init
window.bookMe = {
    store: {},
    Event: new Event,
    stateManager: new State
};

//Search Results
window.searchResults = new Vue({
    el: '#search-results',
    data: {
        store: window.bookMe.stateManager.getStore(),
        properties: {}
    },
    components: {
        PropertyPreview
    },
    mounted() {
        this.updateResults(window.results);
        this.properties = this.getProperties();
    },
    methods: {
        getProperties(results) {
            return window.bookMe.stateManager.getSearchResults(results).data;
        },
        updateResults() {
            return window.bookMe.stateManager.updateSearchResults(window.results);
        }
    }
});
