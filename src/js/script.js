function setupETPlugin() {
  const $ = jQuery;

  $(function () {
    updateSubCategoriesOnSelect();

    setupTypeaheads();

    setupFilterableDropdowns();

    setupCategoryBehaviour();

    setupCategoryFilter();

    setupChevronToggle();

    setupBrandLogoPreview();

    setupBrandLogoClear();

    setupSpecsTags();

    setupMyListingAdmin();

    $(".et-searchable-dropdown").select2();

    let percent = $(".progress-bar").attr("data-percent");

    percent = 100 - parseInt(percent);

    $(".progress-bar__inner").css({ width: percent + "%" });

    $('input[name="featured"]').on("click", function (e) {
      var $radio = $(this);

      // if this was previously checked
      if ($radio.data("waschecked") == true) {
        $radio.prop("checked", false);
        $radio.data("waschecked", false);
      } else $radio.data("waschecked", true);

      // remove was checked from other radios
      $radio.siblings('input[name="rad"]').data("waschecked", false);
    });

    $("#price-type").on("change", function (event) {
      var isEnteredPrice = event.currentTarget.value === "Entered Price";

      if (isEnteredPrice) {
        $("#price-value-block").removeClass("d-none");

        $("#price-value").attr("required", "required");
      } else {
        $("#price-value-block").addClass("d-none");

        $("#price-value").removeAttr("required");
      }
    });

    const images = document.getElementById("gallery-images");

    images && images.addEventListener("change", previewPhoto);

    $("a[data-reveal]").on("click", revealButton);
  });

  function revealButton(event) {
    event.preventDefault();

    const link = $(event.currentTarget);

    const base64 = link.data("reveal");
    const base64Converted = atob(base64);

    link.attr("href", base64Converted);
    link.text(base64Converted.split(":").splice(1).join(""));
    link.off("click", revealButton);
  }

  function updateSubCategoriesOnSelect() {
    const updateSubCategories = (category) => {
      $("#sub-category").html("");

      if (category != "") {
        let subOptions = $("#category")
          .find('option[value="' + category + '"]')
          .data("options");

        var firstValue = $("#sub-category").data("first");

        $("#sub-category").append(`<option value="">${firstValue}</option>`);

        if (typeof subOptions !== "undefined") {
          subOptions = JSON.parse(atob(subOptions));

          $(subOptions).each(function (i, v) {
            $("#sub-category").append(
              `<option value="${v.term_id}">${v.name}</option>`,
            );
          });
        }

        if ($("#sub-category").data("show-other") === "true")
          $("#sub-category").append(`<option value="Other">Other</option>`);

        $("#sub-category").select2();
      }

      if (category == "Other") {
        $("#other_cat_wrap").removeClass("d-none");
      } else {
        $("#other_cat_wrap").addClass("d-none");
      }
    };

    $("#category").on("change", (e) => {
      let category = $(this).val();

      updateSubCategories(category);
    });

    $("#sub-category").on("change", function (e) {
      let sub_category = $(this).val();

      if (sub_category == "Other") {
        $("#other_subcat_wrap").removeClass("d-none");
      } else {
        $("#other_other_subcat_wrapcat_wrap").addClass("d-none");
      }
    });

    const urlParams = new URLSearchParams(window.location.search);

    const category = urlParams.get("category");
    const sub_category = urlParams.get("sub_category");

    if (category) {
      updateSubCategories(category);
    }

    if (sub_category) {
      $("#sub-category").val(sub_category);
      $("#sub-category").trigger("change");
    }
  }

  function setupTypeaheads() {
    var typeaheads = $(".js-typeahead");

    var substringMatcher = function (strs) {
      return function findMatches(q, cb) {
        var matches, substringRegex;

        // an array that will be populated with substring matches
        matches = [];

        // regex used to determine if a string contains the substring `q`
        substrRegex = new RegExp(q, "i");

        // iterate through the pool of strings and for any string that
        // contains the substring `q`, add it to the `matches` array
        $.each(strs, function (i, str) {
          if (substrRegex.test(str)) {
            matches.push(str);
          }
        });

        cb(matches);
      };
    };

    typeaheads.each(function (index, element) {
      const { mustSelectItem, options } = element.dataset;

      var mappedOptions = JSON.parse(atob(options));

      $(element).typeahead(
        {
          hint: true,
          highlight: true,
          minLength: 1,
        },
        {
          name: "states",
          source: substringMatcher(mappedOptions),
        },
      );
    });
  }

  function setupFilterableDropdowns() {
    $(".dropdown ul input").on("keyup", function () {
      var value = $(this).val().toLowerCase();
      $(this)
        .parent()
        .children("li")
        .filter(function () {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    $("ul.dropdown-menu li").on("click", function () {
      $(this).parent().parent().find(".btn input").val($(this).text());
    });
  }

  function setupCategoryBehaviour() {
    var mainCategoryCollapse = ".main-category .collapse";
    var categoryFilter = ".category-filter";

    $(mainCategoryCollapse).on("show.bs.collapse", function () {
      if ($(categoryFilter).val() === "") {
        $(mainCategoryCollapse).not(this).parent().parent().addClass("d-none");
      }
    });

    $(mainCategoryCollapse).on("hidden.bs.collapse", function () {
      $(mainCategoryCollapse).parent().parent().removeClass("d-none");
    });
  }

  let filterTimer;

  function setupCategoryFilter() {
    $(".category-filter").on("keyup", function () {
      clearTimeout(filterTimer);
      filterTimer = setTimeout(() => {
        var searchText = $(this).val().toLowerCase();

        $(".main-category").each(function () {
          var mainCategory = $(this);
          mainCategory
            .find(".collapse")
            .parent()
            .parent()
            .removeClass("d-none");
          var mainCategoryText = mainCategory.find(".btn").text().toLowerCase();
          var subCategories = mainCategory.find(".sub-categories a");

          var isMainCategoryMatch = mainCategoryText.includes(searchText);
          var isSubCategoryMatch = false;

          subCategories.each(function () {
            var subCategory = $(this);
            var subCategoryText = subCategory.text().toLowerCase();
            var isMatch = subCategoryText.includes(searchText);

            subCategory.parent().toggle(isMatch);
            if (isMatch) isSubCategoryMatch = true;
          });

          mainCategory.toggle(isMainCategoryMatch || isSubCategoryMatch);
          if (searchText === "") {
            mainCategory.find(".collapse").collapse("hide");
          } else if (isSubCategoryMatch) {
            mainCategory.find(".collapse").collapse("show");
          }
        });
      }, 100);
    });
  }

  function setupChevronToggle() {
    var categoryCollapse = "#categoryCollapse";
    var categoryMainHeader = ".category-main-header";
    var collapseIcon = ".collapse-icon";
    var chevronUp = "fa-chevron-up";
    var chevronDown = "fa-chevron-down";

    $(categoryCollapse).on("show.bs.collapse", function () {
      $(categoryMainHeader)
        .find(collapseIcon)
        .removeClass(chevronDown)
        .addClass(chevronUp);
    });

    $(categoryCollapse).on("hidden.bs.collapse", function () {
      $(categoryMainHeader)
        .find(collapseIcon)
        .removeClass(chevronUp)
        .addClass(chevronDown);
    });
  }

  const previewPhoto = (event) => {
    if ($('input[name="images[]"]').length >= 10) {
      alert("You have reached limit of 10 images.");
      event.currentTarget.value = "";
      return false;
    } else {
      const files = event.currentTarget.files;
      if (files.length >= 10) {
        alert("You can only select up to 10 images.");
        event.currentTarget.value = "";
        return false;
      }

      if ($('input[name="images[]"]').length + files.length > 10) {
        alert("You can only select up to 10 images.");
        event.currentTarget.value = "";
        return false;
      }

      const childCount =
        document.getElementById("gallery-preview").childElementCount;

      for (var i = 0; i < files.length; i++) {
        const file = files[i];
        const count = childCount + i;
        const fileReader = createFileReader(count);

        // Create hidden input to store file data
        const input = document.createElement("input");
        input.setAttribute("name", "images[]");
        input.setAttribute("value", ""); // Set value to empty string initially
        input.setAttribute("type", "hidden");
        document.getElementById("file-preview-" + count).appendChild(input);

        updateAddImagesRequired();

        fileReader.readAsDataURL(file);
      }

      event.currentTarget.value = "";
    }
  };

  const removePhoto = (event) => {
    const gallery = document.getElementById("gallery-preview");
    gallery.removeChild(document.getElementById(event.target.id));

    updateAddImagesRequired();
  };

  const createFileReader = (childCount) => {
    const fileReader = new FileReader();

    const preview = document.createElement("img");
    preview.classList.add("col-3");
    preview.classList.add("p-1");
    preview.setAttribute("id", "file-preview-" + childCount);
    preview.addEventListener("click", removePhoto);
    document.getElementById("gallery-preview").appendChild(preview);

    fileReader.onload = function (event) {
      // Set the src attribute of the preview image
      preview.setAttribute("src", event.target.result);

      // Set value of hidden input to data URL
      const hiddenInput = document.querySelector(
        "#file-preview-" + childCount + " input[type='hidden']",
      );
      hiddenInput.setAttribute("value", event.target.result);
    };

    return fileReader;
  };

  function setupBrandLogoPreview() {
    const brandLogo = document.getElementById("brand-logo");
    const preview = document.getElementById("brand-logo-image");

    brandLogo &&
      brandLogo.addEventListener("change", function () {
        if (this.files && this.files[0]) {
          const reader = new FileReader();

          reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove("d-none");
          };

          reader.readAsDataURL(this.files[0]);
        }
      });
  }

  function setupBrandLogoClear() {
    const clearImage = document.getElementById("clear-brand-logo");
    const imagePreview = document.querySelector(".brand-logo-preview");
    const brandLogo = document.getElementById("brand-logo");
    const preview = document.getElementById("brand-logo-image");

    if (imagePreview) {
      imagePreview.addEventListener("mouseover", function () {
        if (preview.src !== "#") {
          clearImage.classList.remove("d-none");
        }
      });

      imagePreview.addEventListener("mouseout", function () {
        clearImage.classList.add("d-none");
      });
    }

    clearImage &&
      clearImage.addEventListener("click", function () {
        preview.src = "#";
        preview.classList.add("d-none");
        this.classList.add("d-none");
        brandLogo.value = "";
      });
  }

  var selectedFiles = [];

  function setupSpecsTags() {
    $("#spec-sheets").on("change", function () {
      if (this.files.length >= 3) {
        alert("You can only select up to 3 documents.");
        this.value = "";
        return false;
      }

      if ($('input[name="specsheets[]"]').length + this.files.length > 3) {
        alert("You can only select up to 3 documents.");
        this.value = "";
        return false;
      }

      Array.from(this.files).forEach((file) => {
        selectedFiles.push(file);
      });

      refreshFileTags();
    });
  }

  function refreshFileTags() {
    let fileTags = $("#file-tags");
    fileTags.empty();

    selectedFiles.forEach((file, i) => {
      let tag = $("<span>").addClass(
        "badge badge-pill badge-light m-2 p-0 d-flex gap-2 align-items-center justify-content-center",
      );

      // Convert file object to Base64 string
      let reader = new FileReader();
      reader.onload = function (event) {
        let fileData = event.target.result;

        // Create hidden input field with Base64-encoded file data
        let hiddenInput = $("<input>").attr({
          type: "hidden",
          name: "specsheets[]", // Change the name if needed
          value: fileData,
        });
        tag.append(hiddenInput);
      };
      reader.readAsDataURL(file);

      let fileName = $("<p>")
        .addClass("mb-0 pl-3")
        .text(file.name)
        .appendTo(tag);
      let closeButton = $("<button>")
        .addClass("btn border-0 pr-3")
        .html('<i class="fas fa-times fa-xs"></i>')
        .on("click", function () {
          // Remove the corresponding tag (including hidden input)
          tag.remove();
          // Remove the file from selectedFiles array
          selectedFiles.splice(i, 1);
        });

      tag.append(closeButton);
      fileTags.append(tag);
    });
  }

  function updateAddImagesRequired() {
    if (document.getElementById("gallery-preview").childElementCount == 0) {
      document
        .getElementById("gallery-images")
        .setAttribute("required", "required");
    } else {
      document.getElementById("gallery-images").removeAttribute("required");
    }
  }

  function setupMyListingAdmin() {
    $("[name=add-to-cart\\[\\]]").on("change", function () {
      if ($("[name=add-to-cart\\[\\]]").is(":checked")) {
        $("[name=my-listings-form-submit]").removeAttr("disabled");
      } else {
        $("[name=my-listings-form-submit]").attr("disabled", "disabled");
      }
    });
  }
}
setupETPlugin();
