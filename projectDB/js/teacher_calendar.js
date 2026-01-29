// teacher_calendar.js
let currentDate = new Date();
let selectedDate = null;

function renderCalendar() {
    const calendar = document.getElementById('calendar');
    const monthText = document.getElementById('monthText');
    
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    monthText.textContent = `${currentDate.toLocaleString('default', { month: 'long' })} ${year}`;
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);//If month starts on Wednesday → go back to Sunday.
    startDate.setDate(startDate.getDate() - firstDay.getDay()); 
    
    const endDate = new Date(lastDay);
    endDate.setDate(endDate.getDate() + (6 - lastDay.getDay()));
    
    calendar.innerHTML = '';
    
    // Days of week header
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    days.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'day-header';
        dayHeader.textContent = day;
        calendar.appendChild(dayHeader);
    });
    
        // Calendar days
    let date = new Date(startDate);
    while (date <= endDate) {
        const dayDiv = document.createElement('div');
        dayDiv.className = 'day';
        if (date.getMonth() !== month) {
            dayDiv.classList.add('other-month');
        }
        dayDiv.dataset.date = date.getFullYear() + '-' + 
            (date.getMonth() + 1).toString().padStart(2, '0') + '-' + 
            date.getDate().toString().padStart(2, '0'); //allows JS to match events with dates
        dayDiv.addEventListener('click', () => openModal(date));
        
        // Create date number element
        const dateNum = document.createElement('div');
        dateNum.textContent = date.getDate();
        dayDiv.appendChild(dateNum);
        
        // Add events
        const dayEvents = events.filter(e => e.start_date === dayDiv.dataset.date);
        if (dayEvents.length > 0) {
            dayDiv.classList.add('has-events');
            const eventTitle = document.createElement('div');
            eventTitle.className = 'event-title';
            eventTitle.textContent = dayEvents[0].title;
            dayDiv.appendChild(eventTitle);
        }
        
        calendar.appendChild(dayDiv);
        date.setDate(date.getDate() + 1);
    }
}

function openModal(date) {
    selectedDate = date;
    document.getElementById('eventId').value = '';
    document.getElementById('title').value = '';
    document.getElementById('description').value = '';
    const dateStr = date.getFullYear() + '-' + (date.getMonth() + 1).toString().padStart(2, '0') + '-' + date.getDate().toString().padStart(2, '0');
    document.getElementById('startDate').value = dateStr;
    document.getElementById('endDate').value = dateStr;
    document.getElementById('startTime').value = '09:00';
    document.getElementById('endTime').value = '10:00';
    document.getElementById('eventModal').style.display = 'block';
}

function editEvent(event) {
    document.getElementById('eventId').value = event.id;
    document.getElementById('title').value = event.title;
    document.getElementById('description').value = event.description;
    document.getElementById('startDate').value = event.start_date;
    document.getElementById('endDate').value = event.end_date;
    document.getElementById('startTime').value = event.start_time;
    document.getElementById('endTime').value = event.end_time;
    document.getElementById('eventModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('eventModal').style.display = 'none';
}

function prevMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

// Add navigation buttons
document.addEventListener('DOMContentLoaded', () => {
    const monthYear = document.getElementById('monthYear');
    const prevBtn = document.createElement('button');
    prevBtn.textContent = '<';
    prevBtn.addEventListener('click', prevMonth);
    const nextBtn = document.createElement('button');
    nextBtn.textContent = '>';
    nextBtn.addEventListener('click', nextMonth);
    const monthText = document.createElement('span');
    monthText.id = 'monthText';
    monthYear.appendChild(prevBtn);
    monthYear.appendChild(monthText);
    monthYear.appendChild(nextBtn);
    
    renderCalendar();
});