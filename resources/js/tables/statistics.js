require('../bootstrap');

const app = new Vue({
  el: "#app",
  data: {
    table: table,
    tables: tables,
    category_id, category_id,
    area: area,
    opened: {
      regions: [],
      areas: []
    },
    selectedTable: selectedTable,
    loading: false,
    check: null,
  },
  methods: {
    generateTable: function() {
      let component = this;
      let url = location.protocol + '//' + location.host + location.pathname + '/generate';
      this.loading = true;

      this.check = setInterval(() => {
                      this.checkNewTables();
                    }, 5000);
      
      axios.post(url, {category_id: this.category_id, area: this.area});
    },
    checkNewTables: function() {
      let component = this;
      let url = location.protocol + '//' + location.host + location.pathname;
      
      axios.post(url, {isJson: true}).then(function(response) {
        if(Object.keys(component.tables).length != Object.keys(response.data.tables).length) {
          component.loading = false;
          clearInterval(component.check);
        }

        component.tables = response.data.tables;
      });
    }
  },
  watch: {
    selectedTable: function(value) {
      let component = this;
      let url = location.protocol + '//' + location.host + location.pathname;
      axios.post(url, {isJson: true, table: value}).then(function(response) {
        component.table = response.data.table;
      });
    }
  },
  created: function() {

  }
});