//constants
const schedDate = document.getElementById("sched-date-label");
const schedRoom = document.getElementById("sched-room-label");
const selectedWeekLabel = document.getElementById("calendar-selected-date");
//current time
const today = new Date()

const weekDay = today.toLocaleDateString("en-US", {
  weekday: "short"
}); // e.g Wed, Sat

const dayNum = today.getDay(); // e.g Friday = 5 ; counted from 0-6 with Sunday as 0 and Saturday as 6

const weekDayButton = "#" + weekDay.toLowerCase() + "-button";

let formattedSchedDate = today.toLocaleDateString("en-US", {
  weekday: "long",
  month: "long",
  day: "numeric",
  year: "numeric"
}); // e.g Friday, May 29, 2026

let firstDayLabel = "";
let lastDayLabel = "";

//dom load defaults
window.addEventListener("DOMContentLoaded", () => {
  const defaultWeekButton = document.querySelector(weekDayButton);
  const defaultViewStateButton = document.querySelector("#day-view-button");

  if (defaultWeekButton) defaultWeekButton.classList.add("active");
  if (defaultViewStateButton) defaultViewStateButton.classList.add("active");

  updateWeekButtons();
  schedDateLabelRefresh();
});

//booking-form

//room-selection
const floors = {
    floor1: [101, 102, 103, 104],
    floor2: [201, 202, 203, 204],
    floor3: [301, 302, 303, 304],
    floor4: [401, 402, 403, 404],
}

const floorSelect = document.getElementById("booking-floor")
const roomSelect = document.getElementById("booking-room")

floorSelect.addEventListener("change", function () {
  const selectedFloor = this.value;

  roomSelect.innerHTML = '<option value="" selected disabled hidden>Select Room</option>';

  if (floors[selectedFloor]) {
    floors[selectedFloor].forEach(item => {
      const option = document.createElement("option");
      option.value = item;
      option.textContent = item;
      roomSelect.appendChild(option);
    });
  }
  const defaultRoom = "###"
  schedRoomLabelRefresh(defaultRoom);
});

roomSelect.addEventListener("change", function () {
  const selectedRoom = this.value;
  schedRoomLabelRefresh(selectedRoom);
})

//additional-notes
const textarea = document.getElementById("add-notes")
const counter = document.getElementById("counter")

textarea.addEventListener("input", function () {
    counter.textContent = this.value.length + " / 500 characters"
});

//submit-button
const bookingForm = document.querySelector(".booking-form");

bookingForm.addEventListener("submit", function (event) {
  event.preventDefault();

  console.log("Form submission blocked — checking availability first");

  alert("PHP and SQL still not finished")
  checkAvailability();
});

//live-calendar-mainframe

//calendar-navbar
const prevBtn = document.getElementById("calendar-prev-btn")
const nextBtn = document.getElementById("calendar-next-btn")

prevBtn.addEventListener("click", () => {
  weekOffset--;
  updateWeekButtons();
  weekButtons.forEach(b => b.classList.remove("active"));
  weekButtons[0].classList.add("active");
  schedDateLabelRefresh();
});

nextBtn.addEventListener("click", () => {
  weekOffset++;
  updateWeekButtons();
  weekButtons.forEach(b => b.classList.remove("active"));
  weekButtons[0].classList.add("active");
  schedDateLabelRefresh();
});

selectedWeekLabel.textContent = `${firstDayLabel} - ${lastDayLabel}`;

//week-buttons
const weekButtons = document.querySelectorAll(".week-buttons button");

let weekOffset = 0;

weekButtons.forEach(btn => {
  btn.addEventListener("click", () => {

      weekButtons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      schedDateLabelRefresh();
    });
});

function updateWeekButtons() {
  const sunday = new Date(today);
  sunday.setDate(today.getDate() - dayNum + (weekOffset * 7))

  weekButtons.forEach((btn, i) => {
    const currentDate = new Date(sunday)
    currentDate.setDate(sunday.getDate() + i);

    if (i === 0) {
      firstDayLabel = currentDate.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric"
      })
    }
    else if (i === 6) {
      lastDayLabel = currentDate.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric"
      })
    }

    btn.innerHTML = currentDate.toLocaleDateString("en-US", {
      weekday: "short",
      month: "short",
      day: "numeric"
    }).replace(",", "<br>");
    btn.dataset.date = currentDate.toISOString();

    selectedWeekLabel.textContent = `${firstDayLabel} - ${lastDayLabel}`;
})}

function getActiveWeekButton () {
  return Array.from(weekButtons).find(btn =>
    btn.classList.contains("active")
  );
}

function getActiveWeekDate () {
  const activeBtn = getActiveWeekButton();
  if (!activeBtn) return null;
  return new Date(activeBtn.dataset.date);
}

//schedule-navbar-btns
const viewStateButtons = document.querySelectorAll(".view-state-buttons");

viewStateButtons.forEach(btn => {
    btn.addEventListener("click", () => {
        viewStateButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
    });
});

//schedule-labels
function schedDateLabelRefresh() {
  const dateObj = getActiveWeekDate();
  if (!dateObj) return;

  formattedSchedDate = dateObj.toLocaleDateString("en-US", {
    weekday: "long",
    month: "long",
    day: "numeric",
    year: "numeric"
  });
  schedDate.textContent = "Schedule for " + formattedSchedDate
}

function schedRoomLabelRefresh(selectedRoom) {
  schedRoom.textContent = "Room " + selectedRoom
}

//schedule-graph
function timeToMinutes(time) {
  const [h, m] = time.split(":").map(Number);
  return h * 60 + m;
} //still not finished