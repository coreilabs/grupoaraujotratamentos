(function ($) {
  "use strict";

  const $field = $("#gat-midias");
  const $preview = $("#gat-media-preview");

  if (!$field.length || !window.wp?.media) {
    return;
  }

  function getUrls() {
    return $field
      .val()
      .split(/\r?\n/)
      .map((url) => url.trim())
      .filter(Boolean);
  }

  function isVideo(url) {
    return /\.(mp4|webm|ogg)(?:\?.*)?$/i.test(url);
  }

  function renderPreview() {
    const urls = getUrls();
    $preview.empty();

    urls.forEach((url, index) => {
      const $item = $("<div>", { class: "gat-media-item" });
      const $media = isVideo(url)
        ? $("<video>", { src: url, muted: true, preload: "metadata" })
        : $("<img>", { src: url, alt: "" });
      const $remove = $("<button>", {
        type: "button",
        class: "button-link-delete gat-remove-media",
        text: "Remover",
        "data-index": index,
      });

      $item.append($media, $remove);
      $preview.append($item);
    });
  }

  $("#gat-select-media").on("click", function (event) {
    event.preventDefault();

    const frame = wp.media({
      title: "Selecionar imagens ou vídeos da unidade",
      button: { text: "Adicionar à galeria" },
      library: { type: ["image", "video"] },
      multiple: true,
    });

    frame.on("select", function () {
      const urls = getUrls();

      frame
        .state()
        .get("selection")
        .each(function (attachment) {
          const url = attachment.toJSON().url;
          if (url && !urls.includes(url)) {
            urls.push(url);
          }
        });

      $field.val(urls.join("\n")).trigger("change");
    });

    frame.open();
  });

  $preview.on("click", ".gat-remove-media", function () {
    const urls = getUrls();
    urls.splice(Number($(this).data("index")), 1);
    $field.val(urls.join("\n")).trigger("change");
  });

  $("#gat-clear-media").on("click", function () {
    if (window.confirm("Remover todas as mídias desta galeria?")) {
      $field.val("").trigger("change");
    }
  });

  $field.on("input change", renderPreview);
  renderPreview();
})(jQuery);
