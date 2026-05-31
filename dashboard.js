//constants
const schedDate = document.getElementById("sched-date-label");
const schedRoom = document.getElementById("sched-room-label");
const selectedWeekLabel = document.getElementById("calendar-selected-date");
//current time
const today = new Date();

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

  renderAll();
  updateWeekButtons();
  schedDateLabelRefresh();
});

function renderAll() {
  renderRoomSelection();
};

//booking-form

function createSelectionState() {
  let selectedFloor = null;

  return {
    getFloor: () => selectedFloor,
    setFloor: (value) => {selectedFloor = value;}
  };
};

const state = createSelectionState();

//room-selection
const floors = [
  {
    id: "floor1",
    rooms: []
  },
  {
    id: "floor2",
    rooms: []
  },
  {
    id: "floor3",
    rooms: []
  },
  {
    id: "floor4",
    rooms: []
  },
];
/* data structure needed
  room is the dic in rooms so something like rooms: [{}, {}, {}]
  room = {
    roomNum: 101,
    timeSlots: [
      {
        start: "7:30 AM",
        end: "10:30 AM",
        status: "available", "occupied", "tentative", "unavailable" //these are options
        reservedBy: None,
        purpose: None
      }
      {etc.}
    ]
  }
*/
let floorCount = 0;

floors.forEach(floor => {
  floorCount++;

  for (let i = 0; i < 5; i++) {
    floor.rooms.push({
      roomNum: `${floorCount}0${i}`,
      timeSlots: []
    })
  }

  floor.rooms.forEach(room => {
    const defaultSlotStartTime = "7:30 AM";
    let startTime = defaultSlotStartTime;
    let endTime = minutesToTime(timeToMinutes(startTime) + 180) //3 hrs
    for (let i = 0; i < 4; i++) {
      room.timeSlots.push({
      start: startTime,
      end: endTime,
      status: "available",
      reservedBy: null,
      purpose: null
    })
    startTime = endTime;
    endTime = minutesToTime(timeToMinutes(startTime) + 180)
    }
  })
});

const floorSelect = document.getElementById("booking-floor");
const roomSelect = document.getElementById("booking-room");


floorSelect.addEventListener("change", function () {
  state.setFloor(this.value);

  renderRoomOptions();
});

roomSelect.addEventListener("change", function () {
  const selectedRoom = this.value;
  schedRoomLabelRefresh(selectedRoom);
  
  const floor = floors.find(f => f.id === state.getFloor());
  const room = floor.rooms.find(r => r.roomNum === selectedRoom)
  console.log(room.timeSlots)
  renderSlots(room.timeSlots)
});

function renderFloorOptions() {
  floorSelect.innerHTML = `<option value="" selected disabled hidden>Select Floor</option>`
  floors.forEach(floor => {
    const floorOption = document.createElement("option");
    floorOption.value = floor.id;
    let floorNum = toOrdinal(Number(floor.id.replace("floor", "")))
    floorOption.textContent = floorNum + " Floor";
    floorSelect.appendChild(floorOption);
  })
  state.setFloor(null)
};

function renderRoomOptions() {
  roomSelect.innerHTML = '<option value="" selected disabled hidden>Select Room</option>';

  const floor = floors.find(f => f.id === state.getFloor());

  if (floor) {
    floor.rooms.forEach(room => {
      const roomOption = document.createElement("option");
      roomOption.value = room.roomNum;
      roomOption.textContent = room.roomNum;
      roomSelect.appendChild(roomOption);
    });
  }
  const defaultRoom = "###"
  schedRoomLabelRefresh(defaultRoom);
};

function renderRoomSelection() {
  renderFloorOptions();
  renderRoomOptions();
};

//additional-notes
const textarea = document.getElementById("add-notes");
const counter = document.getElementById("counter");

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
const prevBtn = document.getElementById("calendar-prev-btn");
const nextBtn = document.getElementById("calendar-next-btn");

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
      renderAll();
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
})};

function getActiveWeekButton () {
  return Array.from(weekButtons).find(btn =>
    btn.classList.contains("active")
  );
};

function getActiveWeekDate () {
  const activeBtn = getActiveWeekButton();
  if (!activeBtn) return null;
  return new Date(activeBtn.dataset.date);
};

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
};

function schedRoomLabelRefresh(roomNum) {
  schedRoom.textContent = "Room " + roomNum
};

//schedule-graph
function renderSlots(roomTimeSlots) {
  const grid = document.getElementById("schedule-grid")

  while (grid.firstChild) {
    grid.removeChild(grid.firstChild);
  }

  roomTimeSlots.forEach(timeSlot => {
    const roomStartTime = timeSlot.start
    const roomEndTime = timeSlot.end
    const roomStatus = timeSlot.status
    const slot = document.createElement("div")
    slot.value = `${roomStartTime} - ${roomEndTime}`;
    slot.textContent = `${roomStartTime} - ${roomEndTime}`;
    slot.classList.add(`${roomStatus}-slot`)
    grid.appendChild(slot);
  })
};

//utility functions
function toOrdinal(num) {
  let suffix = "th";

  if ((num % 10) === 1 && (num % 100) !== 11) {
    suffix = "st";
  }
  else if ((num % 10) === 2 && (num % 100) !== 12) {
    suffix = "nd";
  }
  else if ((num % 10) === 3 && (num % 100) !== 13) {
    suffix = "rd";
  }

  return num + suffix;
};

function timeToMinutes(time) {
  let [hours, rest] = time.split(":");
  let [minutes, period] = rest.trim().split(" ");

  hours = Number(hours);
  minutes = Number(minutes);

  if (period === "PM" && hours !== 12) {
    hours += 12;
  }
  else if (period === "AM" && hours === 12) {
    hours = 0;
  }
  return hours * 60 + minutes;
};

function minutesToTime(minutes) {
  let h = Math.floor(minutes / 60);
  let m = minutes % 60;
  
  let period = "AM";
  
  if (h >= 12) {
    period = "PM";
  }

  h = h % 12; 
  if (h === 0) {h = 12}; // edge case for 0:00 midnight, not needed but just there

  m = String(m).padStart(2, "0");

  return `${h}:${m} ${period}`;
};
