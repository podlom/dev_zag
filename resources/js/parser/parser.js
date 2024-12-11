require('../bootstrap');

const app = new Vue({
  el: '#parser',
  data: {
    logs: logs,
    log: log,
    log_id: log? log.id : null,
    only_new: false,
    jobs_left: jobs_left,
    check_jobs_count: null,
    per_page: 10,
    current_page: 1,
    type: 'product',
  },
  computed: {
    filtered_table: function() {
      if(!this.log || !this.log.details)
        return [];

      let items = this.log.details;

      items = items.filter(item => item.type === this.type);

      if(this.only_new)
        items = items.filter(item => item.is_new);

      if(this.per_page != -1)
        return items.filter((item, key) => (key >= this.per_page * (this.current_page - 1)) && key < this.per_page * this.current_page);

      return items;
    },
    last_page: function() {
      if(!this.log || !this.log.details)
        return 0;

      let items = this.log.details;

      items = items.filter(item => item.type === this.type);

      if(this.only_new)
        items = items.filter(item => item.is_new);

      return Math.ceil(Object.keys(items).length / this.per_page);
    }
  },
  methods: {
    startParsing: function() {
      let component = this;
      axios.post('/admin/parser/parse').then(function(response) {
        component.jobs_left = 1;
        component.startCheckingJobsCount();
      });
    },
    getJobsCount: function() {
      let component = this;
      axios.post('/admin/parser', {isJson: true}).then(function(response) {
        component.jobs_left = response.data.jobs_left;
        if(!component.jobs_left) {
          component.logs = response.data.logs;
          clearInterval(component.check_jobs_count);
        }
      });
    },
    startCheckingJobsCount: function() {
      this.check_jobs_count = setInterval(() => {
        this.getJobsCount();
      }, 5000);
    }
  },
  watch: {
    log_id: function(value) {
      let component = this;
      axios.post('/admin/parser/getLog', {id: value}).then(function(response) {
        component.log = response.data;
        component.only_new = false;
        component.current_page = 1;
      });
    },
    only_new: function() {
      this.current_page = 1;
    },
    per_page: function() {
      this.current_page = 1;
    },
    type: function() {
      this.current_page = 1;
    }
  },
  created: function(){
    console.log('parser vue created');
    if(this.jobs_left)
      this.startCheckingJobsCount();
  }
});