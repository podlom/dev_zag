var pollMix = {
  data: function () {
    return {
      poll: poll,
      poll_voted: poll_voted,
      poll_answers: poll_answers,
      product_id: product_original_id,
      pollSuccess: pollSuccess,
      selectedAnswers: [],
    }
  },
  methods: {
    vote: function() {
      if(!this.selectedAnswers)
        return;

      let component = this;
      let val = typeof this.selectedAnswers == 'number'? [this.selectedAnswers] : this.selectedAnswers;
      let question_id = this.poll.original_id? this.poll.original_id : this.poll.id;
      
      axios.post('/pollAnswer', {answers: val, product_id: this.product_id, question_id: question_id}).then(function(response) {
        component.poll_voted = true;
        component.poll_answers = response.data.poll_answers;
        noty('success', component.pollSuccess);
      });
    },
    answerVotes: function(option) {
      if(option.original_id)
        return this.poll_answers[option.original_id]? this.poll_answers[option.original_id] : 0;

      return this.poll_answers[option.id]? this.poll_answers[option.id] : 0;
    }
  },
  created: function() {
    console.log('pollMix created');
  }
}

export default pollMix;