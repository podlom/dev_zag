var notificationsMix = {
  data: function () {
    return {
      notifications: null,
      notificationsCollection: null,
      notificationsSeen: JSON.parse(localStorage.getItem('zagorodna_noty_seen'))? JSON.parse(localStorage.getItem('zagorodna_noty_seen')) : [],
      hasNew: false,
    }
  },
  methods: {
    addToNotifications: function(item, type) {
      let id = item.original_id? item.original_id : item.id;

      if(this.notifications[type].includes(id)) {
        this.notifications[type].splice(this.notifications[type].indexOf(id), 1);
        noty('success', 'Удалено');
        return;
      }

      this.notifications[type].push(id);
      noty('success', 'Добавлено');
    },
    // validateNoty: function(event) {
    //   if(!this.totalNotifications) {
    //     noty('error', 'Список уведомлений пуст');
    //     return event.preventDefault();
    //   }
    // },
    isSeen: function(item) {
      return this.notificationsSeen.includes(item.id);
    },
    checkIfNew: function() {
      let component = this;
      let isNew = false;

      Object.keys(this.notificationsCollection).forEach(function(key) {
        if(!component.isSeen(component.notificationsCollection[key]))
          isNew = true;
      });

      this.hasNew = isNew;
    }
  },
  computed: {
    totalNotifications: function() {
      return this.notificationsCollection? this.notificationsCollection.length : null;
    },
  },
  watch: {
    notifications: {
      handler: function(value) {
        localStorage.zagorodna_noty = JSON.stringify(value);
      },
      deep: true
    },
    notificationsSeen: {
      handler: function(value) {
        localStorage.zagorodna_noty_seen = JSON.stringify(value);
        this.checkIfNew();;
      },
      deep: true
    }
  },
  created: function() {
    let component = this;
    let notifications = JSON.parse(localStorage.getItem('zagorodna_noty'));
    
    if(!notifications)
      notifications = {};

    if(!notifications['products'])
      notifications['products'] = [];

    if(!notifications['companies'])
      notifications['companies'] = [];
    
    this.notifications = notifications;

	window.addEventListener("load", function(event) {
      setTimeout(() => {
        axios.post('/getNotifications', {notifications : component.notifications}).then(function(response) {
          component.notificationsCollection = response.data.notifications;
          component.checkIfNew();
        });
      }, 3000);
    })
  }
}

export default notificationsMix;