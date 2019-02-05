var Session = function(prefix, version) {
    this.version = version || 0;
    this.prefix = prefix || ''
}
Session.prototype.store = function(key, value) {
    localStorage.setItem([this.prefix, key, this.version].join('-'), JSON.stringify(value));
}
Session.prototype.retrieve = function(key) {
    return JSON.parse(localStorage.getItem([this.prefix, key, this.version].join('-')));
}

export default Session;
