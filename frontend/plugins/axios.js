export default function({ app, $axios, redirect }) {
  $axios.onResponse(response => {
    /*if (response.data.message && !response.config.hide_toast)
      app.$toast.success(response.data.message);*/
  });

  $axios.onError(error => {
    /*if (error.response.data.message)
      app.$toast.error(error.response.data.message);*/
  });
}