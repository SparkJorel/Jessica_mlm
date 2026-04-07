"""
Generate the RACS HEALTH GLOBAL / Jessica MLM USER MANUAL as a styled PDF.
Run: python generate_user_manual.py
Output: MANUEL_UTILISATEUR_RACS.pdf  (next to this script)
"""
import os
import sys

try:
    from reportlab.lib.pagesizes import A4
    from reportlab.lib.units import cm, mm
    from reportlab.lib import colors
    from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
    from reportlab.lib.enums import TA_LEFT, TA_CENTER, TA_JUSTIFY
    from reportlab.platypus import (
        BaseDocTemplate, PageTemplate, Frame, Paragraph, Spacer,
        Image, Table, TableStyle, PageBreak, KeepTogether, ListFlowable,
        ListItem, NextPageTemplate, HRFlowable,
    )
    from reportlab.pdfgen import canvas as canvaslib
except ImportError:
    print("Installer reportlab : pip install reportlab")
    sys.exit(1)

# ---------- Brand colors (RACS HEALTH GLOBAL) ----------
PURPLE = colors.HexColor("#6b1d8c")     # primaire
PURPLE_DARK = colors.HexColor("#4a1262")
PURPLE_LIGHT = colors.HexColor("#f3e6f7")
GREEN = colors.HexColor("#58c93a")      # accent
GREEN_DARK = colors.HexColor("#2f8a18")
YELLOW = colors.HexColor("#f5e94a")     # bordure
GREY_TXT = colors.HexColor("#3a3a3a")
GREY_LIGHT = colors.HexColor("#f6f6f6")

HERE = os.path.dirname(os.path.abspath(__file__))
LOGO = os.path.normpath(os.path.join(HERE, "..", "..", "..", "logo_racs.jpg"))
OUTPUT = os.path.join(HERE, "MANUEL_UTILISATEUR_RACS.pdf")

# ---------- Styles ----------
def make_styles():
    s = {}
    s["title"] = ParagraphStyle("title", fontName="Helvetica-Bold",
                                fontSize=30, leading=36, textColor=PURPLE,
                                alignment=TA_CENTER, spaceAfter=10)
    s["subtitle"] = ParagraphStyle("subtitle", fontName="Helvetica-Oblique",
                                   fontSize=14, leading=18,
                                   textColor=GREEN_DARK,
                                   alignment=TA_CENTER, spaceAfter=20)
    s["cover_meta"] = ParagraphStyle("cm", fontName="Helvetica",
                                     fontSize=11, leading=14,
                                     textColor=GREY_TXT, alignment=TA_CENTER)
    s["h1"] = ParagraphStyle("h1", fontName="Helvetica-Bold",
                             fontSize=20, leading=24, textColor=colors.white,
                             alignment=TA_LEFT,
                             backColor=PURPLE,
                             borderPadding=(8, 10, 8, 10),
                             spaceBefore=4, spaceAfter=14, leftIndent=0)
    s["h2"] = ParagraphStyle("h2", fontName="Helvetica-Bold",
                             fontSize=14, leading=18, textColor=PURPLE_DARK,
                             spaceBefore=12, spaceAfter=6)
    s["h3"] = ParagraphStyle("h3", fontName="Helvetica-Bold",
                             fontSize=11, leading=14, textColor=GREEN_DARK,
                             spaceBefore=8, spaceAfter=4)
    s["body"] = ParagraphStyle("body", fontName="Helvetica",
                               fontSize=10.5, leading=15, textColor=GREY_TXT,
                               alignment=TA_JUSTIFY, spaceAfter=6)
    s["bullet"] = ParagraphStyle("bullet", parent=s["body"],
                                 leftIndent=14, bulletIndent=4, spaceAfter=3)
    s["callout"] = ParagraphStyle("callout", fontName="Helvetica",
                                  fontSize=10.5, leading=15,
                                  textColor=PURPLE_DARK,
                                  backColor=PURPLE_LIGHT,
                                  borderColor=PURPLE,
                                  borderWidth=0,
                                  borderPadding=(8, 10, 8, 10),
                                  leftIndent=0, rightIndent=0,
                                  spaceBefore=6, spaceAfter=10)
    s["tip"] = ParagraphStyle("tip", fontName="Helvetica-Oblique",
                              fontSize=10, leading=14,
                              textColor=GREEN_DARK,
                              backColor=colors.HexColor("#eaf8e3"),
                              borderPadding=(6, 8, 6, 8),
                              spaceBefore=6, spaceAfter=10)
    s["toc_item"] = ParagraphStyle("toc", fontName="Helvetica",
                                   fontSize=11, leading=18, textColor=GREY_TXT,
                                   leftIndent=10)
    s["footer"] = ParagraphStyle("foot", fontName="Helvetica",
                                 fontSize=8, textColor=colors.grey,
                                 alignment=TA_CENTER)
    return s

ST = make_styles()

# ---------- Page decorations ----------
def cover_page(canvas, doc):
    canvas.saveState()
    w, h = A4
    # yellow border
    canvas.setStrokeColor(YELLOW)
    canvas.setLineWidth(8)
    canvas.rect(15, 15, w - 30, h - 30, stroke=1, fill=0)
    # purple top band
    canvas.setFillColor(PURPLE)
    canvas.rect(15, h - 90, w - 30, 75, stroke=0, fill=1)
    # green bottom band
    canvas.setFillColor(GREEN)
    canvas.rect(15, 15, w - 30, 60, stroke=0, fill=1)
    # tagline at bottom
    canvas.setFillColor(colors.white)
    canvas.setFont("Helvetica-Bold", 13)
    canvas.drawCentredString(w / 2, 40, "Votre santé, notre mission — Votre réussite, notre engagement")
    # year top band
    canvas.setFillColor(colors.white)
    canvas.setFont("Helvetica-Bold", 16)
    canvas.drawCentredString(w / 2, h - 55, "MANUEL DU DISTRIBUTEUR")
    canvas.setFont("Helvetica", 10)
    canvas.drawCentredString(w / 2, h - 75, "Édition 2026")
    canvas.restoreState()

def inner_page(canvas, doc):
    canvas.saveState()
    w, h = A4
    # left vertical purple bar
    canvas.setFillColor(PURPLE)
    canvas.rect(0, 0, 12, h, stroke=0, fill=1)
    # green accent square
    canvas.setFillColor(GREEN)
    canvas.rect(0, h - 60, 12, 60, stroke=0, fill=1)
    # header line
    canvas.setStrokeColor(PURPLE)
    canvas.setLineWidth(0.5)
    canvas.line(2 * cm, h - 1.5 * cm, w - 2 * cm, h - 1.5 * cm)
    canvas.setFillColor(PURPLE)
    canvas.setFont("Helvetica-Bold", 9)
    canvas.drawString(2 * cm, h - 1.3 * cm, "RACS HEALTH GLOBAL")
    canvas.setFillColor(GREEN_DARK)
    canvas.setFont("Helvetica-Oblique", 9)
    canvas.drawRightString(w - 2 * cm, h - 1.3 * cm, "Manuel du distributeur")
    # footer
    canvas.setStrokeColor(GREEN)
    canvas.line(2 * cm, 1.5 * cm, w - 2 * cm, 1.5 * cm)
    canvas.setFillColor(colors.grey)
    canvas.setFont("Helvetica", 8)
    canvas.drawString(2 * cm, 1.1 * cm, "© 2026 RACS Health Global — Tous droits réservés")
    canvas.drawRightString(w - 2 * cm, 1.1 * cm, f"Page {doc.page - 1}")
    canvas.restoreState()

# ---------- Helpers ----------
def p(text, style="body"):
    return Paragraph(text, ST[style])

def callout(text):
    return Paragraph("<b>💡 À retenir :</b> " + text, ST["callout"])

def tip(text):
    return Paragraph("<b>Astuce —</b> " + text, ST["tip"])

def bullets(items):
    flows = [ListItem(Paragraph(t, ST["body"]), leftIndent=10)
             for t in items]
    return ListFlowable(flows, bulletType="bullet", start="•",
                        leftIndent=14, bulletFontSize=10,
                        bulletColor=PURPLE)

def section_header(num, title):
    return Paragraph(f"{num}. {title.upper()}", ST["h1"])

def styled_table(data, col_widths=None, header=True):
    t = Table(data, colWidths=col_widths, repeatRows=1 if header else 0,
              hAlign="LEFT")
    style = [
        ("FONTNAME", (0, 0), (-1, -1), "Helvetica"),
        ("FONTSIZE", (0, 0), (-1, -1), 9.5),
        ("TEXTCOLOR", (0, 0), (-1, -1), GREY_TXT),
        ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
        ("ALIGN", (0, 0), (-1, -1), "LEFT"),
        ("LEFTPADDING", (0, 0), (-1, -1), 8),
        ("RIGHTPADDING", (0, 0), (-1, -1), 8),
        ("TOPPADDING", (0, 0), (-1, -1), 6),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 6),
        ("LINEBELOW", (0, 0), (-1, -1), 0.3, colors.HexColor("#dddddd")),
    ]
    if header:
        style += [
            ("BACKGROUND", (0, 0), (-1, 0), PURPLE),
            ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
            ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
            ("FONTSIZE", (0, 0), (-1, 0), 10),
            ("ROWBACKGROUNDS", (0, 1), (-1, -1),
             [colors.white, GREY_LIGHT]),
        ]
    t.setStyle(TableStyle(style))
    return t

# ---------- Build document ----------
def build():
    doc = BaseDocTemplate(
        OUTPUT, pagesize=A4,
        leftMargin=2.2 * cm, rightMargin=2 * cm,
        topMargin=2.3 * cm, bottomMargin=2.2 * cm,
        title="Manuel du distributeur — RACS Health Global",
        author="RACS Health Global",
    )
    frame_cover = Frame(doc.leftMargin, doc.bottomMargin,
                        doc.width, doc.height, id="cover")
    frame_inner = Frame(doc.leftMargin, doc.bottomMargin,
                        doc.width, doc.height, id="inner")
    doc.addPageTemplates([
        PageTemplate(id="Cover", frames=frame_cover, onPage=cover_page),
        PageTemplate(id="Inner", frames=frame_inner, onPage=inner_page),
    ])

    story = []

    # ===== COVER =====
    story.append(Spacer(1, 2.2 * cm))
    if os.path.exists(LOGO):
        img = Image(LOGO, width=9 * cm, height=9 * cm)
        img.hAlign = "CENTER"
        story.append(img)
    story.append(Spacer(1, 0.6 * cm))
    story.append(Paragraph("RACS HEALTH GLOBAL", ST["title"]))
    story.append(Paragraph("Le marketing de réseau au service de votre santé et de votre liberté financière",
                           ST["subtitle"]))
    story.append(Spacer(1, 0.4 * cm))
    story.append(HRFlowable(width="40%", thickness=2, color=GREEN,
                            hAlign="CENTER"))
    story.append(Spacer(1, 0.4 * cm))
    story.append(Paragraph("Guide complet à l'usage des nouveaux distributeurs",
                           ST["cover_meta"]))
    story.append(Paragraph("Comprendre — S'inscrire — Gagner — Évoluer",
                           ST["cover_meta"]))

    story.append(NextPageTemplate("Inner"))
    story.append(PageBreak())

    # ===== TABLE OF CONTENTS =====
    story.append(Paragraph("SOMMAIRE", ST["h1"]))
    toc = [
        "1.  Bienvenue chez RACS Health Global",
        "2.  Qu'est-ce que le marketing de réseau ?",
        "3.  Comment devenir distributeur",
        "4.  Les packs d'adhésion",
        "5.  Notre catalogue de produits",
        "6.  L'arbre binaire expliqué simplement",
        "7.  Les SV — Volume de ventes",
        "8.  Le plan de rémunération : 5 façons de gagner",
        "9.  Les grades et votre carrière",
        "10. Tableau de bord et retrait de vos gains",
        "11. Règles de conduite et engagement",
        "12. Questions fréquentes (FAQ)",
    ]
    for t in toc:
        story.append(Paragraph(t, ST["toc_item"]))
    story.append(PageBreak())

    # ===== 1. BIENVENUE =====
    story.append(section_header(1, "Bienvenue chez RACS Health Global"))
    story.append(p(
        "Félicitations ! En ouvrant ce manuel, vous faites un premier pas vers une "
        "aventure passionnante : celle de bâtir votre propre activité dans le "
        "domaine de la santé et du bien-être, aux côtés d'une communauté de "
        "distributeurs engagés."))
    story.append(p(
        "<b>RACS Health Global</b> est une société qui propose des produits de "
        "santé naturels et de bien-être, distribués à travers un réseau de "
        "partenaires indépendants. Notre mission est double : offrir au plus "
        "grand nombre des produits de qualité, et permettre à chaque distributeur "
        "de générer des revenus complémentaires — voire un revenu principal — "
        "grâce à un plan de rémunération clair, équitable et motivant."))
    story.append(Paragraph("Notre vision", ST["h2"]))
    story.append(p(
        "Créer une communauté de femmes et d'hommes libres, en bonne santé et "
        "financièrement indépendants, partout en Afrique et au-delà."))
    story.append(Paragraph("Nos valeurs", ST["h2"]))
    story.append(bullets([
        "<b>Santé d'abord</b> — des produits naturels, testés et de qualité.",
        "<b>Transparence</b> — un plan de rémunération sans zones d'ombre.",
        "<b>Équité</b> — chacun gagne en fonction de son travail réel.",
        "<b>Solidarité</b> — votre parrain et votre équipe sont là pour vous aider.",
        "<b>Durabilité</b> — bâtir un revenu sur le long terme, pas un coup d'éclat.",
    ]))
    story.append(callout(
        "Ce manuel vous accompagne pas à pas. Prenez le temps de le lire en "
        "entier avant de vous lancer — vous gagnerez du temps et éviterez "
        "beaucoup d'erreurs courantes."))
    story.append(PageBreak())

    # ===== 2. MLM =====
    story.append(section_header(2, "Qu'est-ce que le marketing de réseau ?"))
    story.append(p(
        "Le <b>marketing de réseau</b> (aussi appelé <b>MLM</b>, pour "
        "<i>Multi-Level Marketing</i>) est une méthode de distribution dans "
        "laquelle les produits ne sont pas vendus en magasin, mais directement "
        "par des distributeurs indépendants — comme vous bientôt."))
    story.append(p(
        "Au lieu de dépenser des millions en publicité télé, l'entreprise "
        "préfère récompenser les personnes qui font connaître les produits "
        "autour d'elles : famille, amis, collègues, voisins, communautés."))
    story.append(Paragraph("Vous gagnez de deux façons", ST["h2"]))
    story.append(bullets([
        "<b>En vendant les produits</b> à des clients : vous achetez au prix "
        "distributeur, vous revendez au prix client, vous gardez la marge.",
        "<b>En parrainant d'autres distributeurs</b> : quand les personnes que "
        "vous avez introduites travaillent à leur tour, vous touchez un "
        "pourcentage sur leur activité — c'est ce qu'on appelle le "
        "<i>revenu de réseau</i>.",
    ]))
    story.append(Paragraph("Pourquoi le « plan binaire » ?", ST["h2"]))
    story.append(p(
        "RACS Health Global utilise un plan dit <b>binaire</b> : chaque "
        "distributeur construit deux équipes (une à gauche, une à droite). "
        "C'est un système simple, équitable et puissant, car votre parrain et "
        "vos parrains supérieurs peuvent vous aider en plaçant de nouveaux "
        "membres sous vous (on appelle cela le <i>spillover</i>). Vous n'êtes "
        "jamais seul."))
    story.append(tip(
        "Le MLM n'est PAS un système pyramidal illégal. Dans une pyramide, "
        "il n'y a pas de vrai produit, et seuls ceux du haut gagnent. Chez "
        "nous, le produit est réel, utile, et chacun peut progresser quel que "
        "soit son point de départ."))
    story.append(PageBreak())

    # ===== 3. INSCRIPTION =====
    story.append(section_header(3, "Comment devenir distributeur"))
    story.append(p(
        "Devenir distributeur RACS Health Global se fait en 4 étapes simples. "
        "L'inscription se fait <b>par l'intermédiaire d'un parrain</b> "
        "(un distributeur déjà actif). Si vous n'avez pas encore de parrain, "
        "contactez notre service client."))
    story.append(Paragraph("Les 4 étapes", ST["h2"]))
    steps = [
        ["1", "Trouver un parrain",
         "Un distributeur déjà actif vous présente l'opportunité et vous accompagne."],
        ["2", "Choisir votre pack",
         "Sélectionnez le pack d'adhésion qui correspond à votre budget et à vos ambitions (voir section 4)."],
        ["3", "Remplir votre dossier",
         "Nom, prénom, téléphone, email, adresse, pièce d'identité. Le tout via votre parrain ou en ligne."],
        ["4", "Régler votre pack",
         "Paiement par Mobile Money (Orange Money, MTN MoMo) via Dohone. Vous recevez un SMS de confirmation."],
    ]
    story.append(styled_table(
        [["#", "Étape", "Ce que vous faites"]] + steps,
        col_widths=[1 * cm, 4.5 * cm, 11 * cm]))
    story.append(Spacer(1, 0.4 * cm))
    story.append(Paragraph("Documents à préparer", ST["h2"]))
    story.append(bullets([
        "Une pièce d'identité valide (CNI, passeport).",
        "Un numéro de téléphone Mobile Money à votre nom.",
        "Une adresse email active (pour recevoir vos identifiants).",
        "Le montant de votre pack d'adhésion en Mobile Money.",
    ]))
    story.append(callout(
        "Une fois votre paiement validé, vous recevez immédiatement vos "
        "identifiants de connexion et l'accès à votre tableau de bord en ligne. "
        "Vous êtes officiellement distributeur RACS Health Global !"))
    story.append(PageBreak())

    # ===== 4. PACKS =====
    story.append(section_header(4, "Les packs d'adhésion"))
    story.append(p(
        "Chaque nouveau distributeur démarre en achetant un <b>pack d'adhésion</b>. "
        "Ce pack contient des produits que vous pourrez consommer ou revendre, "
        "et il détermine également le pourcentage de bonus binaire auquel vous "
        "avez droit. Plus votre pack est élevé, plus vos potentiels de gains "
        "sont importants."))
    story.append(Paragraph("À quoi sert votre pack ?", ST["h2"]))
    story.append(bullets([
        "<b>Vous donnez accès</b> à votre espace distributeur en ligne.",
        "<b>Vous activez</b> votre position dans l'arbre binaire.",
        "<b>Vous recevez</b> un lot de produits (à consommer ou revendre).",
        "<b>Vous générez</b> un volume initial de SV (Volume de Ventes).",
        "<b>Vous fixez</b> votre pourcentage de bonus binaire.",
    ]))
    story.append(Paragraph("Aperçu indicatif des packs", ST["h2"]))
    story.append(p("<i>Les noms et montants exacts sont disponibles auprès "
                   "de votre parrain et sur votre espace en ligne.</i>"))
    pack_data = [
        ["Pack", "Public visé", "Avantages clés"],
        ["Découverte", "Démarrage à petit budget",
         "Idéal pour goûter aux produits et démarrer doucement."],
        ["Standard", "Distributeur régulier",
         "Bon équilibre entre coût et potentiel de gains."],
        ["Business", "Ambitieux et leaders",
         "Pourcentage maximum de bonus binaire et plus de produits."],
        ["VIP / Élite", "Investisseurs et entrepreneurs",
         "Tous les bonus au taux maximum, pack premium complet."],
    ]
    story.append(styled_table(pack_data,
                              col_widths=[3.5 * cm, 5 * cm, 8 * cm]))
    story.append(Spacer(1, 0.3 * cm))
    story.append(tip(
        "Choisissez un pack à la hauteur de vos ambitions, mais "
        "<b>jamais au-dessus de vos moyens</b>. Vous pourrez toujours "
        "passer à un pack supérieur plus tard via une « mise à niveau »."))
    story.append(PageBreak())

    # ===== 5. PRODUITS =====
    story.append(section_header(5, "Notre catalogue de produits"))
    story.append(p(
        "Au cœur de RACS Health Global, il y a nos produits. Sans produit "
        "réel et utile, il n'y aurait pas de vraie activité. Notre gamme "
        "s'articule autour de la santé naturelle et du bien-être quotidien."))
    story.append(Paragraph("Nos gammes principales", ST["h2"]))
    story.append(bullets([
        "<b>Compléments alimentaires</b> — vitamines, minéraux, plantes.",
        "<b>Soins du corps</b> — huiles, baumes, crèmes naturelles.",
        "<b>Bien-être quotidien</b> — tisanes, boissons santé.",
        "<b>Hygiène et beauté</b> — savons, shampoings naturels.",
    ]))
    story.append(Paragraph("Deux prix : distributeur et client", ST["h2"]))
    story.append(p(
        "Chaque produit a deux prix dans notre système : un <b>prix "
        "distributeur</b> (le vôtre) et un <b>prix client</b> (le prix "
        "public conseillé). La différence entre les deux, c'est votre "
        "<b>marge directe</b> sur la vente — encaissée immédiatement, en "
        "plus de tous les bonus du plan de rémunération."))
    example = [
        ["", "Prix", "Pour vous"],
        ["Prix distributeur (votre achat)", "8 000 FCFA", ""],
        ["Prix client (votre vente)", "12 000 FCFA", ""],
        ["Marge directe encaissée", "", "+ 4 000 FCFA"],
    ]
    story.append(Paragraph("Exemple chiffré", ST["h3"]))
    story.append(styled_table(example,
                              col_widths=[7 * cm, 4 * cm, 5 * cm]))
    story.append(callout(
        "Chaque produit vendu vous rapporte aussi des <b>SV</b> (Volume de "
        "Ventes) qui alimentent vos bonus de réseau — voir section 7."))
    story.append(PageBreak())

    # ===== 6. ARBRE BINAIRE =====
    story.append(section_header(6, "L'arbre binaire expliqué simplement"))
    story.append(p(
        "Imaginez un arbre généalogique très simple : vous êtes au sommet, "
        "et en dessous de vous, il n'y a que <b>deux places</b> : une à "
        "gauche et une à droite. Voilà le principe du plan binaire."))
    story.append(Paragraph("Comment ça marche", ST["h2"]))
    story.append(bullets([
        "Chaque distributeur a <b>2 jambes</b> : la jambe <b>gauche</b> "
        "et la jambe <b>droite</b>.",
        "Quand vous parrainez un nouveau distributeur, vous le placez "
        "sous vous, soit à gauche, soit à droite.",
        "Si les deux places directes sont prises, le nouveau filleul "
        "« descend » dans l'arbre jusqu'à trouver une place libre.",
        "Vos parrains supérieurs peuvent aussi placer leurs filleuls "
        "<b>sous vous</b> : c'est le <b>spillover</b> (débordement). Vous "
        "bénéficiez ainsi du travail de votre upline.",
    ]))
    story.append(Paragraph("Le principe de la jambe la plus faible", ST["h2"]))
    story.append(p(
        "Le bonus binaire (votre principale source de revenus de réseau) est "
        "calculé sur la <b>jambe la plus faible</b>. Pourquoi ? Pour vous "
        "encourager à équilibrer vos deux équipes et à aider vos filleuls."))
    story.append(p("Exemple :"))
    bin_example = [
        ["", "Jambe gauche", "Jambe droite"],
        ["Volume de ventes (SV)", "10 000", "6 000"],
        ["Jambe la plus faible", "—", "✓ 6 000 SV"],
        ["Bonus binaire calculé sur", "", "6 000 SV"],
    ]
    story.append(styled_table(bin_example,
                              col_widths=[6 * cm, 5 * cm, 5 * cm]))
    story.append(Spacer(1, 0.3 * cm))
    story.append(p(
        "Le surplus de la jambe la plus forte (ici 4 000 SV à gauche) n'est "
        "<b>pas perdu</b> : il est <b>reporté</b> au cycle suivant grâce au "
        "système de <i>carry-over</i>. Rien ne se perd."))
    story.append(tip(
        "L'erreur classique des débutants : ne parrainer que d'un seul côté. "
        "Pour gagner régulièrement, équilibrez vos efforts sur vos deux jambes."))
    story.append(PageBreak())

    # ===== 7. SV =====
    story.append(section_header(7, "Les SV — Volume de Ventes"))
    story.append(p(
        "Les <b>SV</b> (<i>Sales Volume</i>, ou Volume de Ventes) sont la "
        "<b>monnaie interne</b> du plan de rémunération. Chaque produit et "
        "chaque pack a une valeur en SV. Vos bonus sont calculés à partir "
        "des SV générés par vous et votre équipe."))
    story.append(Paragraph("Comment gagner des SV ?", ST["h2"]))
    story.append(bullets([
        "En achetant un pack d'adhésion (vous recevez un lot de SV de départ).",
        "En vendant des produits à vos clients.",
        "En parrainant de nouveaux distributeurs (leurs achats génèrent des SV qui remontent dans votre arbre).",
        "En faisant vos propres réachats mensuels (consommation personnelle).",
    ]))
    story.append(Paragraph("Cycles et report (carry-over)", ST["h2"]))
    story.append(p(
        "Les SV sont comptabilisés par <b>cycles</b> (en général "
        "hebdomadaires ou mensuels selon le bonus). À la fin de chaque cycle :"))
    story.append(bullets([
        "Les SV des deux jambes sont comparés.",
        "Le bonus est calculé sur la jambe la plus faible.",
        "Les SV non payés sont <b>reportés</b> au cycle suivant — ils ne "
        "sont jamais perdus tant que vous restez actif.",
    ]))
    story.append(callout(
        "Pour rester actif et conserver vos SV, vous devez maintenir un "
        "petit volume mensuel personnel (votre « maintenance »). Votre "
        "espace distributeur vous le rappelle automatiquement."))
    story.append(PageBreak())

    # ===== 8. PLAN DE REMUNERATION =====
    story.append(section_header(8, "Le plan de rémunération : 5 façons de gagner"))
    story.append(p(
        "Chez RACS Health Global, vous pouvez gagner de l'argent de "
        "<b>cinq manières différentes</b>. Plus vous activez de sources, "
        "plus votre revenu devient stable et important."))

    bonuses = [
        ("① Marge sur la vente directe",
         "Vous achetez au prix distributeur, vous revendez au prix client. "
         "La différence est pour vous, encaissée tout de suite. C'est votre "
         "premier revenu, le plus rapide à activer."),
        ("② Bonus de parrainage (bonus direct)",
         "Chaque fois qu'une personne que vous avez parrainée achète un "
         "pack ou monte de niveau, vous touchez un pourcentage immédiat "
         "sur ce pack. C'est la récompense de votre travail de prospection."),
        ("③ Bonus binaire (bonus de paire)",
         "C'est le cœur du plan. À chaque cycle, le système compare votre "
         "jambe gauche et votre jambe droite, et vous verse un pourcentage "
         "sur les SV de la jambe la plus faible. Le pourcentage exact "
         "dépend de votre pack d'adhésion."),
        ("④ Bonus indirect (bonus de niveau)",
         "Vous touchez un petit pourcentage sur les achats de produits "
         "effectués par les distributeurs sur plusieurs niveaux sous vous, "
         "même s'ils ne sont pas vos filleuls directs."),
        ("⑤ Bonus générationnel (leadership)",
         "Réservé aux distributeurs qui atteignent les grades supérieurs. "
         "Vous touchez un pourcentage sur l'activité de plusieurs "
         "« générations » de leaders sous vous. C'est le bonus qui crée "
         "les revenus passifs durables."),
    ]
    for title, txt in bonuses:
        story.append(Paragraph(title, ST["h2"]))
        story.append(p(txt))

    story.append(callout(
        "Plus votre pack est élevé, plus les pourcentages de bonus qui vous "
        "sont attribués sont importants. C'est pourquoi le choix du pack "
        "(section 4) est une décision stratégique."))
    story.append(PageBreak())

    # ===== 9. GRADES =====
    story.append(section_header(9, "Les grades et votre carrière"))
    story.append(p(
        "Au fur et à mesure que vous développez votre équipe et vos ventes, "
        "vous montez en <b>grade</b>. Chaque grade débloque de nouveaux "
        "avantages : pourcentages plus élevés, bonus supplémentaires, "
        "récompenses spéciales, voyages, voitures…"))
    story.append(Paragraph("Comment monter en grade ?", ST["h2"]))
    story.append(bullets([
        "Atteindre un certain <b>volume de SV</b> sur votre équipe.",
        "Avoir un certain nombre de <b>filleuls actifs</b> dans chaque jambe.",
        "Maintenir un volume mensuel personnel (la « maintenance »).",
    ]))
    story.append(Paragraph("Aperçu de la progression", ST["h2"]))
    story.append(p("<i>Les noms exacts des grades et les seuils sont "
                   "consultables dans votre espace distributeur.</i>"))
    grades = [
        ["Niveau", "Profil", "Récompenses typiques"],
        ["Débutant", "Vient de s'inscrire", "Accès au plan, premières marges."],
        ["Confirmé", "Premiers filleuls actifs", "Bonus de parrainage et binaire."],
        ["Leader", "Petite équipe structurée", "Bonus indirect activé."],
        ["Manager", "Équipe en croissance", "Bonus générationnel niveau 1."],
        ["Directeur", "Réseau important", "Primes leadership, voyages."],
        ["Élite / Diamant", "Top du réseau", "Tous les bonus, voiture, primes annuelles."],
    ]
    story.append(styled_table(grades,
                              col_widths=[3 * cm, 5 * cm, 8.5 * cm]))
    story.append(Spacer(1, 0.3 * cm))
    story.append(tip(
        "Une fois un grade atteint, vous le conservez tant que vous "
        "respectez votre maintenance mensuelle. Certains grades supérieurs "
        "sont définitivement acquis : c'est votre pension RACS."))
    story.append(PageBreak())

    # ===== 10. TABLEAU DE BORD & RETRAITS =====
    story.append(section_header(10, "Tableau de bord et retrait de vos gains"))
    story.append(p(
        "Tout se passe sur votre <b>espace distributeur en ligne</b>, "
        "accessible depuis votre téléphone ou votre ordinateur. Vos "
        "identifiants vous sont envoyés dès la validation de votre inscription."))
    story.append(Paragraph("Ce que vous y trouvez", ST["h2"]))
    story.append(bullets([
        "Votre <b>solde de bonus</b> en temps réel.",
        "Votre <b>arbre généalogique</b> (toute votre équipe en un coup d'œil).",
        "Le <b>volume SV</b> de chacune de vos jambes.",
        "Vos <b>commandes</b> et celles de votre équipe.",
        "L'historique de vos <b>retraits</b> et de vos paiements.",
        "Votre <b>grade actuel</b> et les conditions pour passer au suivant.",
    ]))
    story.append(Paragraph("Comment retirer votre argent", ST["h2"]))
    story.append(p(
        "Les retraits se font par <b>Mobile Money</b> (Orange Money, MTN "
        "MoMo) via notre prestataire de paiement <b>Dohone</b>. Le processus "
        "est simple :"))
    withdraw = [
        ["1", "Allez dans « Mes gains » → « Demander un retrait »."],
        ["2", "Saisissez le montant et votre numéro Mobile Money."],
        ["3", "Validez avec le code de confirmation reçu par SMS."],
        ["4", "L'argent est crédité sur votre Mobile Money."],
    ]
    story.append(styled_table(withdraw, col_widths=[1 * cm, 15 * cm], header=False))
    story.append(Spacer(1, 0.3 * cm))
    story.append(callout(
        "Un seuil minimum de retrait peut s'appliquer (consultez votre "
        "espace pour le montant en vigueur). Aucune somme n'est perdue : "
        "tant que votre solde est inférieur au seuil, il s'accumule."))
    story.append(PageBreak())

    # ===== 11. REGLES DE CONDUITE =====
    story.append(section_header(11, "Règles de conduite et engagement"))
    story.append(p(
        "Pour préserver la confiance de tous, RACS Health Global impose à "
        "chaque distributeur quelques règles simples mais essentielles."))
    story.append(Paragraph("Ce que vous devez faire", ST["h2"]))
    story.append(bullets([
        "Présenter les produits et le plan <b>honnêtement</b>, sans promesses irréalistes.",
        "Ne <b>jamais garantir</b> de gains fixes à un prospect.",
        "Respecter les <b>prix officiels</b> des produits.",
        "Protéger les <b>données personnelles</b> de votre équipe.",
        "Accompagner et former vos filleuls.",
    ]))
    story.append(Paragraph("Ce qui est interdit", ST["h2"]))
    story.append(bullets([
        "Faire de fausses déclarations sur les produits (santé, guérison miracle…).",
        "Recruter avec des promesses de gains rapides ou d'enrichissement immédiat.",
        "Spammer ou harceler des prospects.",
        "Revendre les produits en dessous du prix officiel.",
        "Créer de faux comptes ou multiplier les inscriptions sous votre nom.",
    ]))
    story.append(callout(
        "Tout manquement grave peut entraîner la suspension ou la "
        "résiliation du compte distributeur, avec perte des bonus en cours."))
    story.append(PageBreak())

    # ===== 12. FAQ =====
    story.append(section_header(12, "Questions fréquentes (FAQ)"))
    faqs = [
        ("Combien coûte l'inscription ?",
         "L'inscription elle-même est gratuite. Vous achetez simplement un pack d'adhésion (à partir du pack Découverte). Le montant exact dépend du pack choisi."),
        ("Puis-je travailler à temps partiel ?",
         "Oui. Beaucoup de nos distributeurs commencent en parallèle de leur emploi. Vous gérez votre rythme."),
        ("Faut-il être commercial pour réussir ?",
         "Non. Les meilleurs résultats viennent souvent de personnes qui n'ont jamais vendu, mais qui croient aux produits et savent les recommander à leurs proches."),
        ("Quand vais-je recevoir mes premiers gains ?",
         "Votre première marge directe peut tomber dès votre première vente. Les bonus de réseau commencent à arriver dès vos premiers filleuls actifs."),
        ("Que se passe-t-il si j'arrête ?",
         "Vous pouvez arrêter à tout moment. Si vous cessez votre maintenance mensuelle, votre compte devient inactif mais vos données sont conservées."),
        ("Puis-je changer de pack ?",
         "Oui. Vous pouvez « monter en pack » à tout moment en payant la différence. C'est même conseillé quand vos résultats progressent."),
        ("Et si mon parrain ne m'aide pas ?",
         "Contactez son parrain (votre upline). Tout votre upline a intérêt à votre réussite — c'est la beauté du plan binaire."),
        ("Les produits sont-ils certifiés ?",
         "Oui. Tous nos produits respectent les normes en vigueur et sont accompagnés de leur fiche technique."),
        ("Comment suis-je payé ?",
         "Par Mobile Money (Orange Money, MTN MoMo) via Dohone, directement sur votre numéro de téléphone."),
        ("À qui poser mes questions ?",
         "À votre parrain en priorité, puis à votre upline, puis au service client RACS Health Global."),
    ]
    for q, a in faqs:
        story.append(Paragraph("• " + q, ST["h3"]))
        story.append(p(a))

    story.append(Spacer(1, 0.5 * cm))
    story.append(HRFlowable(width="60%", thickness=1.5, color=GREEN,
                            hAlign="CENTER"))
    story.append(Spacer(1, 0.3 * cm))
    story.append(Paragraph(
        "<b>Bienvenue dans la famille RACS Health Global !</b>",
        ParagraphStyle("end", fontName="Helvetica-Bold", fontSize=14,
                       textColor=PURPLE, alignment=TA_CENTER)))
    story.append(Spacer(1, 0.2 * cm))
    story.append(Paragraph(
        "Votre santé, votre liberté, votre réussite — commencent maintenant.",
        ParagraphStyle("endi", fontName="Helvetica-Oblique", fontSize=11,
                       textColor=GREEN_DARK, alignment=TA_CENTER)))

    doc.build(story)
    print(f"PDF généré : {OUTPUT}")


if __name__ == "__main__":
    build()
