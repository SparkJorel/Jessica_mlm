"""Convertit DOCUMENTATION.md en DOCUMENTATION.pdf via reportlab.
Usage: python md_to_pdf.py
"""
import os
import re
import sys

try:
    from reportlab.lib.pagesizes import A4
    from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
    from reportlab.lib.units import cm
    from reportlab.lib import colors
    from reportlab.platypus import (
        SimpleDocTemplate, Paragraph, Spacer, Preformatted,
        PageBreak, Table, TableStyle, ListFlowable, ListItem,
    )
    from reportlab.lib.enums import TA_LEFT
except ImportError:
    print("reportlab manquant. Installation : pip install reportlab")
    sys.exit(1)

HERE = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(HERE, "DOCUMENTATION.md")
DST = os.path.join(HERE, "DOCUMENTATION.pdf")


def escape(text: str) -> str:
    text = text.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")
    # inline code
    text = re.sub(r"`([^`]+)`",
                  r'<font name="Courier" color="#b03030">\1</font>', text)
    # bold
    text = re.sub(r"\*\*([^*]+)\*\*", r"<b>\1</b>", text)
    # italic
    text = re.sub(r"(?<!\*)\*([^*]+)\*(?!\*)", r"<i>\1</i>", text)
    # links [text](url)
    text = re.sub(r"\[([^\]]+)\]\(([^)]+)\)",
                  r'<link href="\2" color="blue">\1</link>', text)
    return text


def build_styles():
    base = getSampleStyleSheet()
    styles = {
        "body": ParagraphStyle("body", parent=base["BodyText"],
                               fontName="Helvetica", fontSize=10,
                               leading=13, spaceAfter=6, alignment=TA_LEFT),
        "h1": ParagraphStyle("h1", parent=base["Heading1"],
                             fontName="Helvetica-Bold", fontSize=20,
                             textColor=colors.HexColor("#1a3d6d"),
                             spaceBefore=18, spaceAfter=12),
        "h2": ParagraphStyle("h2", parent=base["Heading2"],
                             fontName="Helvetica-Bold", fontSize=15,
                             textColor=colors.HexColor("#1a3d6d"),
                             spaceBefore=14, spaceAfter=8),
        "h3": ParagraphStyle("h3", parent=base["Heading3"],
                             fontName="Helvetica-Bold", fontSize=12,
                             textColor=colors.HexColor("#2b5a9e"),
                             spaceBefore=10, spaceAfter=6),
        "h4": ParagraphStyle("h4", parent=base["Heading4"],
                             fontName="Helvetica-Bold", fontSize=11,
                             textColor=colors.HexColor("#444444"),
                             spaceBefore=8, spaceAfter=4),
        "code": ParagraphStyle("code", parent=base["Code"],
                               fontName="Courier", fontSize=8,
                               leading=10, leftIndent=10,
                               backColor=colors.HexColor("#f4f4f4"),
                               borderColor=colors.HexColor("#dddddd"),
                               borderWidth=0.5, borderPadding=4,
                               spaceBefore=4, spaceAfter=8),
        "quote": ParagraphStyle("quote", parent=base["BodyText"],
                                fontName="Helvetica-Oblique", fontSize=10,
                                leftIndent=15, textColor=colors.HexColor("#555555"),
                                spaceAfter=6),
    }
    return styles


def parse_md(path):
    with open(path, "r", encoding="utf-8") as f:
        return f.readlines()


def render(lines, styles):
    story = []
    i = 0
    in_code = False
    code_buf = []
    table_buf = []

    def flush_table():
        nonlocal table_buf
        if not table_buf:
            return
        # parse markdown table
        rows = []
        for raw in table_buf:
            cells = [c.strip() for c in raw.strip().strip("|").split("|")]
            rows.append(cells)
        # remove separator row (---)
        rows = [r for r in rows if not all(re.match(r"^:?-+:?$", c or "-") for c in r)]
        if rows:
            wrapped = [[Paragraph(escape(c), styles["body"]) for c in r] for r in rows]
            t = Table(wrapped, repeatRows=1, hAlign="LEFT")
            t.setStyle(TableStyle([
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#1a3d6d")),
                ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
                ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
                ("GRID", (0, 0), (-1, -1), 0.3, colors.grey),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("ROWBACKGROUNDS", (0, 1), (-1, -1),
                 [colors.white, colors.HexColor("#f7f7f7")]),
            ]))
            story.append(t)
            story.append(Spacer(1, 8))
        table_buf = []

    while i < len(lines):
        line = lines[i].rstrip("\n")

        # fenced code blocks
        if line.strip().startswith("```"):
            if in_code:
                flush_table()
                story.append(Preformatted("\n".join(code_buf), styles["code"]))
                code_buf = []
                in_code = False
            else:
                flush_table()
                in_code = True
            i += 1
            continue
        if in_code:
            code_buf.append(line)
            i += 1
            continue

        # tables
        if line.strip().startswith("|") and "|" in line.strip()[1:]:
            table_buf.append(line)
            i += 1
            continue
        else:
            flush_table()

        # headings
        m = re.match(r"^(#{1,4})\s+(.*)$", line)
        if m:
            level = len(m.group(1))
            text = escape(m.group(2))
            story.append(Paragraph(text, styles[f"h{level}"]))
            i += 1
            continue

        # blockquote
        if line.startswith(">"):
            story.append(Paragraph(escape(line.lstrip("> ").strip()), styles["quote"]))
            i += 1
            continue

        # bullet list
        if re.match(r"^\s*[-*]\s+", line):
            items = []
            while i < len(lines) and re.match(r"^\s*[-*]\s+", lines[i]):
                txt = re.sub(r"^\s*[-*]\s+", "", lines[i].rstrip("\n"))
                items.append(ListItem(Paragraph(escape(txt), styles["body"]),
                                      leftIndent=12))
                i += 1
            story.append(ListFlowable(items, bulletType="bullet", start="•",
                                      leftIndent=14))
            story.append(Spacer(1, 4))
            continue

        # numbered list
        if re.match(r"^\s*\d+\.\s+", line):
            items = []
            while i < len(lines) and re.match(r"^\s*\d+\.\s+", lines[i]):
                txt = re.sub(r"^\s*\d+\.\s+", "", lines[i].rstrip("\n"))
                items.append(ListItem(Paragraph(escape(txt), styles["body"]),
                                      leftIndent=12))
                i += 1
            story.append(ListFlowable(items, bulletType="1", leftIndent=14))
            story.append(Spacer(1, 4))
            continue

        # blank
        if not line.strip():
            story.append(Spacer(1, 4))
            i += 1
            continue

        # paragraph (collect consecutive non-empty lines)
        buf = [line]
        i += 1
        while i < len(lines):
            nxt = lines[i].rstrip("\n")
            if (not nxt.strip() or nxt.startswith("#") or nxt.startswith(">")
                    or nxt.strip().startswith("```")
                    or nxt.strip().startswith("|")
                    or re.match(r"^\s*[-*]\s+", nxt)
                    or re.match(r"^\s*\d+\.\s+", nxt)):
                break
            buf.append(nxt)
            i += 1
        story.append(Paragraph(escape(" ".join(buf)), styles["body"]))

    flush_table()
    if in_code and code_buf:
        story.append(Preformatted("\n".join(code_buf), styles["code"]))
    return story


def main():
    if not os.path.exists(SRC):
        print(f"Introuvable : {SRC}")
        sys.exit(1)
    print(f"Lecture  : {SRC}")
    lines = parse_md(SRC)
    styles = build_styles()
    story = render(lines, styles)

    doc = SimpleDocTemplate(
        DST, pagesize=A4,
        leftMargin=2 * cm, rightMargin=2 * cm,
        topMargin=2 * cm, bottomMargin=2 * cm,
        title="Documentation Jessica MLM (JTWC)",
        author="RACS Global",
    )

    def footer(canvas, doc_):
        canvas.saveState()
        canvas.setFont("Helvetica", 8)
        canvas.setFillColor(colors.grey)
        canvas.drawString(2 * cm, 1 * cm, "Jessica MLM — Documentation technique")
        canvas.drawRightString(A4[0] - 2 * cm, 1 * cm, f"Page {doc_.page}")
        canvas.restoreState()

    doc.build(story, onFirstPage=footer, onLaterPages=footer)
    print(f"PDF généré : {DST}")


if __name__ == "__main__":
    main()
