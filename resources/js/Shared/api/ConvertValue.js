export function formatCurrency(value) {
    const formatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    });

    return formatter.format(value);
}

export function formatNumber(
    amount,
    decimalCount = 2,
    decimal = ".",
    thousands = ","
) {
    try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        amount = Number(amount.toString().replaceAll(",", ""));
        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(
            (amount = Math.abs(amount || 0).toFixed(decimalCount))
        ).toString();
        let j = i.length > 3 ? i.length % 3 : 0;

        return (
            negativeSign +
            (j ? i.substring(0, j) + thousands : "") +
            i.substring(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) +
            (decimalCount
                ? decimal +
                  Math.abs(amount - i)
                      .toFixed(decimalCount)
                      .slice(2)
                : "")
        );
    } catch (e) {
        console.log(e);
    }
}

export function formatPercentage(
    value,
    precision,
    string = false,
    convert = false
) {
    let negative = false;

    if (precision === undefined) {
        precision = 0;
    }

    if (value < 0) {
        negative = true;
        value = value * -1;
    }

    let multiplier = Math.pow(10, precision);
    value = parseFloat((value * multiplier).toFixed(11));
    if (convert) {
        value = value * 100;
    }
    value = (Math.round(value) / multiplier).toFixed(precision);

    if (negative) {
        value = (value * -1).toFixed(precision);
    }

    if (string) {
        value = value + "%";
    }

    return value;
}

export function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

export async function replaceSymbol(string) {
    console.log(string);
    let symbols = getBraceContent(string);
    let res = await axios.post("/api/brace-content", { data: symbols });
    res.data.forEach((result) => {
        if (!result.svg) {
            return;
        }

        string = string.replace(
            result.symbolText,
            `
                <svg width="16" height="16" class="inline">
                    <image xlink:href="${result.svg}" width="16" height="16"/>
                </svg>
            `
        );
    });
    return string;
}

export function replaceLineBreak(string) {
    return string.replace(/\r\n|\r|\n/g, "<br>");
}

export function getBraceContent(string) {
    let found = [];
    let rxp = /{([^}]+)}/g;
    let curMatch;

    while ((curMatch = rxp.exec(string))) {
        found.push(curMatch[1]);
    }

    return found;
}
