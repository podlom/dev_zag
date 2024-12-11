var comparisonMix = {
  data: function () {
    return {
      comparison: null,
    }
  },
  methods: {
    addToComparison: function(item) {
      let id = item.original_id? item.original_id : item.id;

      if(this.comparison.includes(id)) {
        this.comparison.splice(this.comparison.indexOf(id), 1);
        noty('success', 'Удалено');
        return;
      }

      this.comparison.push(id);
      noty('success', 'Добавлено');
    },
  },
  computed: {
    totalComparison: function() {
      return this.comparison.length;
    }
  },
  watch: {
    comparison: {
      handler: function(value) {
        localStorage.zagorodna_comparison = JSON.stringify(this.comparison);
      },
      deep: true
    },
  },
  created: function() {
    console.log('comparison mixin');
    let compare = JSON.parse(localStorage.getItem('zagorodna_comparison'));
    
    if(!compare)
      compare = [];

    this.comparison = compare;
  }
}

export default comparisonMix;