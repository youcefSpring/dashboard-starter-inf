/**
 * Dependency-free SVG charts. Colours come from CSS variables, so they follow the theme.
 *  - [data-sparkline]   small trend line                        (x-sparkline)
 *  - [data-mini-line]   compact line with a dot per point        (x-mini-line-chart)
 *  - [data-line-chart]  multi-series line chart with tooltip     (x-line-chart)
 */

const clamp = (v, min, max) => Math.min(Math.max(v, min), max);
const compact = new Intl.NumberFormat('en-US', { notation: 'compact', maximumFractionDigits: 1 });
const integer = new Intl.NumberFormat('en-US');
const parseDate = (iso) => new Date(`${iso}T00:00:00`);
const shortDate = (iso) => parseDate(iso).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
const longDate = (iso) => parseDate(iso).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });

function niceMax(max, ticks = 4) {
    const raw = max / ticks;
    const exponent = 10 ** Math.floor(Math.log10(raw));
    const fraction = raw / exponent;
    const step = (fraction <= 1 ? 1 : fraction <= 2 ? 2 : fraction <= 2.5 ? 2.5 : fraction <= 5 ? 5 : 10) * exponent;
    return { max: step * ticks, step };
}

/** Monotone cubic interpolation: smooth lines that never overshoot the data. */
function monotonePath(points) {
    const n = points.length;
    if (n < 2) return '';
    const dx = [];
    const slope = [];
    for (let i = 0; i < n - 1; i++) {
        dx[i] = points[i + 1][0] - points[i][0];
        slope[i] = (points[i + 1][1] - points[i][1]) / dx[i];
    }
    const tangent = [slope[0]];
    for (let i = 1; i < n - 1; i++) {
        tangent[i] = slope[i - 1] * slope[i] <= 0
            ? 0
            : (3 * (dx[i - 1] + dx[i])) / ((2 * dx[i] + dx[i - 1]) / slope[i - 1] + (dx[i] + 2 * dx[i - 1]) / slope[i]);
    }
    tangent[n - 1] = slope[n - 2];

    let d = `M${points[0][0].toFixed(1)},${points[0][1].toFixed(1)}`;
    for (let i = 0; i < n - 1; i++) {
        const h = dx[i] / 3;
        d += ` C${(points[i][0] + h).toFixed(1)},${(points[i][1] + tangent[i] * h).toFixed(1)} ${(points[i + 1][0] - h).toFixed(1)},${(points[i + 1][1] - tangent[i + 1] * h).toFixed(1)} ${points[i + 1][0].toFixed(1)},${points[i + 1][1].toFixed(1)}`;
    }
    return d;
}

let gradientId = 0;

function drawSparkline(el) {
    const values = JSON.parse(el.dataset.sparkline);
    const color = { success: '--success', error: '--error', primary: '--c1', neutral: '--fg-3' }[el.dataset.tone] || '--fg-3';
    const w = 88;
    const h = 32;
    const min = Math.min(...values);
    const range = Math.max(...values) - min || 1;
    const points = values.map((v, i) => [2 + (i / (values.length - 1)) * (w - 4), 3 + (1 - (v - min) / range) * (h - 6)]);
    const line = monotonePath(points);
    const id = `spark-${++gradientId}`;
    const last = points[points.length - 1];

    el.innerHTML = `<svg width="${w}" height="${h}" viewBox="0 0 ${w} ${h}" class="shrink-0 overflow-visible" aria-hidden="true">
        <defs><linearGradient id="${id}" x1="0" x2="0" y1="0" y2="1"><stop offset="0" style="stop-color:rgb(var(${color}));stop-opacity:.22"/><stop offset="1" style="stop-color:rgb(var(${color}));stop-opacity:0"/></linearGradient></defs>
        <path d="${line} L${w - 2},${h} L2,${h} Z" fill="url(#${id})"/>
        <path d="${line}" fill="none" style="stroke:rgb(var(${color}))" stroke-width="1.75" stroke-linecap="round"/>
        <circle cx="${last[0]}" cy="${last[1]}" r="2.5" style="fill:rgb(var(${color}))"/>
    </svg>`;
}

function drawMiniLine(el) {
    const { values, labels, prefix = '', suffix = '' } = JSON.parse(el.dataset.miniLine);
    const W = 300;
    const H = 64;
    const pad = 8;
    const min = Math.min(...values) * 0.96;
    const max = Math.max(...values) * 1.02;
    const points = values.map((v, i) => [(i / (values.length - 1)) * W, pad + (1 - (v - min) / (max - min || 1)) * (H - pad * 2)]);
    const d = monotonePath(points);
    const id = `mini-${++gradientId}`;

    el.innerHTML = `<svg viewBox="0 0 ${W} ${H}" preserveAspectRatio="none" class="absolute inset-0 h-full w-full overflow-visible" aria-hidden="true">
            <defs><linearGradient id="${id}" x1="0" x2="0" y1="0" y2="1"><stop offset="0" style="stop-color:rgb(var(--c1));stop-opacity:.18"/><stop offset="1" style="stop-color:rgb(var(--c1));stop-opacity:0"/></linearGradient></defs>
            <path d="${d} L${W},${H} L0,${H} Z" fill="url(#${id})"/>
            <path d="${d}" fill="none" style="stroke:rgb(var(--c1))" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linejoin="round"/>
        </svg>
        ${points.map((p, i) => `<span class="absolute size-2.5 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-surface ${i === points.length - 1 ? 'bg-c1 ring-2 ring-c1/25' : 'bg-c1/70'}" style="left:${(p[0] / W) * 100}%;top:${p[1]}px" data-tip="${labels[i]}: ${prefix}${values[i]}${suffix}"></span>`).join('')}`;
}

function lineChart(el) {
    const data = JSON.parse(el.dataset.lineChart);
    const root = el.closest('[data-line-chart-root]') || el.parentElement;
    const visible = new Set(data.series.map((s) => s.key));
    const uid = ++gradientId;
    let hover = null;

    function draw() {
        const W = el.clientWidth;
        const H = el.clientHeight;
        if (!W || !H) return;

        const n = data.labels.length;
        const series = data.series.filter((s) => visible.has(s.key));
        const axis = series[0];
        const narrow = W < 520;
        const pad = { l: 46, r: narrow ? 6 : 10, t: 10, b: 26 };
        const iw = W - pad.l - pad.r;
        const ih = H - pad.t - pad.b;
        const X = (i) => pad.l + (n === 1 ? iw / 2 : (i / (n - 1)) * iw);
        const scales = Object.fromEntries(series.map((s) => [s.key, niceMax(Math.max(...s.values, 1) * 1.04)]));
        const Y = (v, key) => pad.t + ih * (1 - v / scales[key].max);

        let grid = '';
        let labels = '';
        for (let t = 0; t <= 4; t++) {
            const y = pad.t + ih * (1 - t / 4);
            grid += `<line x1="${pad.l}" x2="${W - pad.r}" y1="${y}" y2="${y}" ${t ? 'stroke-dasharray="3 5"' : ''}/>`;
            if (axis) labels += `<text x="${pad.l - 10}" y="${y + 4}" text-anchor="end" class="chart-axis">${compact.format(scales[axis.key].step * t)}</text>`;
        }
        const ticks = Math.min(narrow ? 4 : 7, n);
        for (let k = 0; k < ticks; k++) {
            const i = Math.round((k * (n - 1)) / Math.max(ticks - 1, 1));
            const anchor = k === 0 ? 'start' : k === ticks - 1 ? 'end' : 'middle';
            labels += `<text x="${X(i)}" y="${H - 6}" text-anchor="${anchor}" class="chart-axis">${shortDate(data.labels[i])}</text>`;
        }

        let defs = '';
        let lines = '';
        let dots = '';
        series.slice().reverse().forEach((s) => {
            const color = `--${s.color}`;
            const d = monotonePath(s.values.map((v, i) => [X(i), Y(v, s.key)]));
            const main = s === axis;
            defs += `<linearGradient id="lc-${uid}-${s.key}" x1="0" x2="0" y1="0" y2="1"><stop offset="0" style="stop-color:rgb(var(${color}));stop-opacity:.2"/><stop offset="1" style="stop-color:rgb(var(${color}));stop-opacity:0"/></linearGradient>`;
            if (main) lines += `<path d="${d} L${X(n - 1)},${pad.t + ih} L${X(0)},${pad.t + ih} Z" fill="url(#lc-${uid}-${s.key})"/>`;
            lines += `<path d="${d}" fill="none" style="stroke:rgb(var(${color}))" stroke-width="${main ? 2.25 : 1.75}" stroke-linecap="round" stroke-linejoin="round"/>`;
            dots += `<circle data-dot="${s.key}" r="4.5" style="fill:rgb(var(--surface));stroke:rgb(var(${color}))" stroke-width="2.25"/>`;
        });

        el.innerHTML = `<svg width="${W}" height="${H}" class="absolute inset-0 block select-none" aria-hidden="true">
                <defs>${defs}</defs><g class="chart-grid">${grid}</g>${labels}${lines}
                <g data-hover style="display:none"><line data-hover-line y1="${pad.t}" y2="${pad.t + ih}" style="stroke:rgb(var(--border-strong))" stroke-dasharray="4 4"/>${dots}</g>
                <rect x="${pad.l}" y="0" width="${iw}" height="${H}" fill="transparent" data-hit/>
            </svg>
            <div data-chart-tip class="pointer-events-none absolute top-2 z-10 hidden min-w-[176px] rounded-lg border border-line bg-surface p-3 text-[13px] shadow-pop"></div>`;

        const hoverGroup = el.querySelector('[data-hover]');
        const hoverLine = el.querySelector('[data-hover-line]');
        const tip = el.querySelector('[data-chart-tip]');

        const show = (i) => {
            hover = i;
            if (i === null || !series.length) {
                hoverGroup.style.display = 'none';
                tip.classList.add('hidden');
                return;
            }
            const x = X(i);
            hoverGroup.style.display = '';
            hoverLine.setAttribute('x1', x);
            hoverLine.setAttribute('x2', x);
            series.forEach((s) => {
                const dot = el.querySelector(`[data-dot="${s.key}"]`);
                dot.setAttribute('cx', x);
                dot.setAttribute('cy', Y(s.values[i], s.key));
            });

            tip.innerHTML = `<p class="mb-2 text-xs font-semibold text-fg-2">${longDate(data.labels[i])}</p>
                ${series.map((s) => `<div class="flex items-center justify-between gap-4 py-0.5"><span class="flex items-center gap-2 text-fg-2"><span class="size-2 rounded-full ${s.dot}"></span>${s.label}</span><span class="num font-semibold">${integer.format(s.values[i])}</span></div>`).join('')}`;
            tip.classList.remove('hidden');
            const tw = tip.offsetWidth;
            tip.style.left = `${x + 14 + tw > W ? x - tw - 14 : x + 14}px`;
        };

        const hit = el.querySelector('[data-hit]');
        const indexFrom = (event) => {
            const box = hit.getBoundingClientRect();
            return clamp(Math.round(((event.clientX - box.left) / box.width) * (n - 1)), 0, n - 1);
        };
        hit.addEventListener('pointermove', (event) => show(indexFrom(event)));
        hit.addEventListener('pointerdown', (event) => show(indexFrom(event)));
        hit.addEventListener('pointerleave', () => show(null));

        el.onkeydown = (event) => {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End', 'Escape'].includes(event.key)) return;
            event.preventDefault();
            const cur = hover ?? n - 1;
            show({
                Escape: null,
                Home: 0,
                End: n - 1,
                ArrowLeft: clamp(cur - 1, 0, n - 1),
                ArrowRight: clamp(cur + 1, 0, n - 1),
            }[event.key]);
        };
        el.onblur = () => show(null);
    }

    root.querySelectorAll('[data-series-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const key = button.dataset.seriesToggle;
            if (visible.has(key)) visible.delete(key);
            else visible.add(key);
            button.setAttribute('aria-pressed', String(visible.has(key)));
            draw();
        });
    });

    let lastSize = '';
    new ResizeObserver(() => {
        const size = `${el.clientWidth}x${el.clientHeight}`;
        if (el.clientWidth && size !== lastSize) {
            lastSize = size;
            draw();
        }
    }).observe(el);
}

export function initCharts() {
    document.querySelectorAll('[data-sparkline]').forEach(drawSparkline);
    document.querySelectorAll('[data-mini-line]').forEach(drawMiniLine);
    document.querySelectorAll('[data-line-chart]').forEach(lineChart);
}
