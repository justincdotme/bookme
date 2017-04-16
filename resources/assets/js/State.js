export default class State {
    updateSearchResults(results) {
        window.bookMe.store.searchResults = {
            data: results.data,
            total: results.total,
            currentPage: results.current_page,
            perPage: parseInt(results.per_page),
            nextPageUrl: results.next_page_url,
            prevPageUrl: results.prev_page_url
        };
    }
    getSearchResults() {
        return window.bookMe.store.searchResults;
    }
    getStore() {
        return window.bookMe.store;
    }
}