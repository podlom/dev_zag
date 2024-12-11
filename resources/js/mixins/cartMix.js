var cartMix = {
  data: function () {
    return {
      cart: cart,
      cartLength: null,
      total: null,
      totalDiscount: null,
    }
  },
  methods: {
    addToCart: function(product){
      if(this.cart[product.id])
        this.cart[product.id].amount = parseInt(this.cart[product.id].amount) + product.amount;
      else {
        let newProduct = product;
        newProduct.size = product.sizes[0];
        newProduct.color = product.attrs[1][0]['name'];
        Vue.set(this.cart, product.id, Object.assign({}, newProduct));
      }
        
        
      // noty('success', 'Товар добавлен в корзину.');
      noty('success', '<ul class="add-to-card__list"><li class="add-to-card__item"><div class="add-to-card__header">\
              <p>Товар добавлен в корзину</p><button class="cansel-add" onclick="document.querySelector(`.product' + product.id + '`).click()">Отменить добавление</button></div>\
          <div class="add-to-card__body"><div class="img" style="background-image: url(' + product.image + ')"></div>\
              <div class="info"><p class="name">' + product.name + '</p><div class="wrapper">' + 
              (product.old_price? ('<p class="old-price">' + product.old_price + ' грн</p>') : '') +
              '<p class="price">' + product.price + ' грн</p>\
                  </div></div></div><div class="add-to-card__footer"><a href="/cart" class="main-button js-button-decor">\
                  <span class="text">перейти в корзину</span></a></div></li></ul>');

    },
    deleteFromCart: function(id) {
      Vue.delete(this.cart, id);
      noty('success', 'Товар удалён из корзины.');
    },
    updateCart: function() {
      axios.post('/cart/update', {data : Object.assign({}, this.cart)});
    },
    countCartLength: function() {
      let total = 0;
      let component = this;
      Object.keys(this.cart).forEach(function(key) {
        if(key != 'complects')
          total += component.cart[key].amount;
        else {
          component.cart[key].forEach(function(item) {
            total += Object.keys(item).length;
          });
        }
      });
      this.cartLength = total;
    },
    validate: function(event) {
      if(!this.cartLength) {
        noty('error', cartErrorMessage);
        return event.preventDefault();
      }
    },
    calcTotal: function() {
      let total = 0;
      let component = this;
      Object.keys(this.cart).forEach(function(key) {
        if(key != 'complects')
          total += component.cart[key].price * component.cart[key].amount;
        else {
          component.cart[key].forEach(function(complect) {
            Object.keys(complect).forEach(function(key) {
              total += complect[key].complect_price? complect[key].complect_price : complect[key].price
            });
          });
        }
      });
      this.total = total;
    },
    calcTotalDiscount: function() {
      let total = 0;
      let component = this;
      Object.keys(this.cart).forEach(function(key) {
        if(key != 'complects')
          total += component.cart[key].old_price? component.cart[key].discount_amount * component.cart[key].amount : 0;
        else {
          component.cart[key].forEach(function(complect) {
            Object.keys(complect).forEach(function(key) {
              total += complect[key].complect_price? complect[key].price - complect[key].complect_price : 0;
            });
          });
        }
      });
      this.totalDiscount = total;
    },
  },
  watch: {
    cart: {
      handler: function(value) {
        this.countCartLength();
        this.calcTotal();
        this.calcTotalDiscount();
        this.updateCart();
      },
      deep: true
    }
  },
  created: function() {
    this.countCartLength();
    this.calcTotal();
    this.calcTotalDiscount();
    console.log('cart', this.cart);
  }
}

export default cartMix;