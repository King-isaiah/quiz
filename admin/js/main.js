// HTTP Request
function xhr(args = {}) {
  const $args = {
    data: {},
    dataType: "json",
    methodType: "POST",
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
  };
  param = $.extend($args, args);

  return $.ajax({
    url: param.url,
    method: param.methodType,
    data: param.data,
    dataType: param.dataType,
    headers: {
      "Content-Type": param.contentType,
    },
    beforeSend: () => {
      if (param.before && typeof param.before === "function") param.before();
    },
    progress: (event) => {
      if (param.progress && typeof param.progress === "function")
        param.progress(event);
    },
  });
}

function resolver(promise, callback) {
  promise.then(
    (data) => callback(data),
    (error) => console.log("something went wrong", error)
  );
}