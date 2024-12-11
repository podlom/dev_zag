var recentMix = {
  data: function () {
    return {
      product: product,
    }
  },
  methods: {

  },
  watch: {

  },
  created: function() {
    console.log('recent mixin');
    if(this.product.category_id !== 2 && this.product.category_id !== 7)
      return;

    let recent = JSON.parse(localStorage.getItem('zagorodna_recent'));

    if(!recent)
      recent = [];

    if(recent.includes(this.product.id))
      recent.splice(recent.indexOf(this.product.id), 1);

    if(recent.includes(this.product.original_id))
      recent.splice(recent.indexOf(this.product.original_id), 1);
      
    recent.unshift(this.product.id);

    if(recent.length > 12)
      recent.pop();

    localStorage.zagorodna_recent = JSON.stringify(recent);
  }
}

export default recentMix;